<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Google\Cloud\Translate\TranslateClient;

class Editor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library([ "ion_auth", "form_validation", "twig" ]);
        $this->load->helper([ "url" ]);
        $this->load->model("product_model");
        $this->load->model("project_model");
        if (! $this->ion_auth->logged_in()) {
            redirect("/login", "refresh");
        }
        $user = $this->ion_auth->user()->row();
        if ($user) {
            $user->image = md5(strtolower(trim($this->ion_auth->user()->row()->email)));
            $user->is_admin = $this->ion_auth->is_admin();
            $this->twig->addGlobal("user", $user);
        }
    }
    
    public $num_required_approvals = 2;
    
    public $breadcrumbs = [
        [
            "label" => "Projects",
            "url" => "/projects",
        ],
    ];
    
    public function index($project_id, $section_id)
    {
        $section = $this->product_model->getSection($section_id);
        $project = $this->project_model->getProject($project_id);
        $product = $this->product_model->getProduct($project["product_id"]);
        
        if (! $project or ! $section) {
            show_404();
        }
        
        $data = [
            "content" => $this->product_model->getSectionContent($project_id, $section_id, $this->ion_auth->user()->row()->id),
            "project" => $project,
            "product" => $product,
            "section" => $section,
            "can_commit" => $this->_can_commit($project_id),
            "can_always_commit" => $this->_can_always_commit($project_id),
            "can_review" => $this->_can_review($project_id),
            "can_auto_translate" => $this->_can_auto_translate($project),
            "is_reviewer" => $this->_is_reviewer($project_id),
            "num_required_approvals" => $this->num_required_approvals,
        ];
        $this->breadcrumbs[] = [ "label" => $product["name"] . " (" . $project["language_name"] . ")", "url" => "/projects/" . $project["id"]  ];
        $this->breadcrumbs[] = [ "label" => $section["name"] ];
        
        $this->twig->addGlobal("title", "Editor");
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/editor", $data);
    }
    
    public function commit()
    {
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("project_id", "Project ID", "required|numeric");
        $this->form_validation->set_rules("content_id", "Content ID", "required|numeric");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        
        $data = $this->input->post();
        $data["user_id"] = $this->ion_auth->user()->row()->id;
        
        $exists = $this->db->select("*")
            ->where("product_content.id", $data["content_id"])
            ->join("projects", "product_content.product_id = projects.product_id")
            ->where("projects.id", $data["project_id"])
            ->from("product_content")
            ->count_all_results();
        
        if (! $exists) {
            show_404();
        }
        
        $this->db->insert("product_content_revisions", $data);
        $id = $this->db->insert_id();
        
        if (! $this->_can_always_commit($data["project_id"])) {
            $this->project_model->updateContentStatus($data["content_id"], $data["project_id"], false);
            $this->project_model->removeContentApprovals($data["content_id"], $data["project_id"]);
        }
        
        $member_exists = $this->db->select("*")
            ->from("project_members")
            ->where("project_id", $data["project_id"])
            ->where("user_id", $data["user_id"])
            ->count_all_results();
        
        if (! $member_exists) {
            $this->project_model->addMember($data["user_id"], $data["project_id"], "translator");
        }
        
        $this->output->set_output(json_encode([ "success" => "Paragraph committed" ]));
    }
    
    public function approve()
    {
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("project_id", "Project ID", "required|numeric");
        $this->form_validation->set_rules("content_id", "Content ID", "required|numeric");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        
        $data = $this->input->post();
        
        if (! $this->_can_review($data["project_id"])) {
            show_404();
        }
        
        $can_always_edit = $this->_can_always_commit($data["project_id"]);
        
        $data["user_id"] = $this->ion_auth->user()->row()->id;
        $data["type"] = "approved";
        
        $this->db->insert("product_content_log", $data);
        $id = $this->db->insert_id();
        
        $resolve_error_data = [
            "resolved_by" => $this->ion_auth->user()->row()->id,
            "resolved_on" => date("Y-m-d H:i:s"),
            "is_resolved" => true,
        ];
        
        $this->db->where("type", "error");
        $this->db->where("project_id", $data["project_id"]);
        $this->db->where("content_id", $data["content_id"]);
        $this->db->update("product_content_log", $resolve_error_data);
        
        $this->project_model->addContentApproval($data["content_id"], $data["project_id"], $this->ion_auth->user()->row()->id);
        
        $approvals = $this->project_model->getContentApprovals($data["content_id"], $data["project_id"]);
        if (count($approvals) >= $this->num_required_approvals) {
            $this->project_model->updateContentStatus($data["content_id"], $data["project_id"], true);
        }
        
        $this->output->set_output(json_encode([ "reviewer_name" => $this->ion_auth->user()->row()->first_name . " " . $this->ion_auth->user()->row()->last_name, "lock_editing" => ! $can_always_edit, "total_approvals" => count($approvals) ]));
    }
    
    public function suggest_revision()
    {
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("project_id", "Project ID", "required|numeric");
        $this->form_validation->set_rules("content_id", "Content ID", "required|numeric");
        $this->form_validation->set_rules("comment", "comment", "required");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        
        $data = $this->input->post();
        
        if (! $this->_can_review($data["project_id"])) {
            show_404();
        }
        
        $data["user_id"] = $this->ion_auth->user()->row()->id;
        $data["type"] = "error";
        
        $this->db->insert("product_content_log", $data);
        $id = $this->db->insert_id();
        
        $this->project_model->updateContentStatus($data["content_id"], $data["project_id"], false);
        $this->project_model->removeContentApprovals($data["content_id"], $data["project_id"]);
        
        $this->output->set_output(json_encode([ "redirect" => "/editor/" . $data["project_id"] . "/" . $this->product_model->getSectionId($data["content_id"]) . "#p" . $data["content_id"] ]));
    }
    
    public function resolve()
    {
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("log_id", "Log ID", "required|numeric");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        
        $data = $this->input->post();
        $error = $this->product_model->getContentLog($data["log_id"]);
        
        if (! $this->_can_review($error["project_id"])) {
            show_404();
        }
        
        $update_data = [
            "resolved_by" => $this->ion_auth->user()->row()->id,
            "resolved_on" => date("Y-m-d H:i:s"),
            "is_resolved" => true,
        ];
        
        $this->db->where("id", $data["log_id"]);
        $this->db->update("product_content_log", $update_data);
        $this->output->set_output(json_encode([ "success" => "Issue resolved" ]));
    }
    
    public function translate()
    {
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("project_id", "Project ID", "required|numeric");
        $this->form_validation->set_rules("content_id", "Content ID", "required|numeric");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        
        $data = $this->input->post();
        $data["user_id"] = $this->ion_auth->user()->row()->id;
        $content = $this->project_model->getContent($data["content_id"]);
        $project = $project = $this->db->select("projects.*, languages.google_code as google_code")
            ->from("projects")
            ->join("languages", "projects.language_id = languages.id")
            ->where("projects.id", $data["project_id"])
            ->get()
            ->row_array();
        
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . APPPATH . "google_translate_authentication.json");
        $client = new TranslateClient();

        $targetLanguage = $project["google_code"];
        $result = $client->translate($content["content"], [
            "target" => $targetLanguage,
            "format" => "text",
        ]);
        $this->output->set_output(json_encode([ "translated_text" => $result["text"] ]));
    }
    
    public function restore($revision_id)
    {
        $this->output->set_content_type("application/json");
        $recovered_revision = $this->db->select("*")
            ->from("product_content_revisions")
            ->where("id", $revision_id)
            ->get()
            ->row_array();
        
        $current_revision = $this->db->select("content")
            ->from("product_content_revisions")
            ->where("content_id", $recovered_revision['content_id'])
            ->where("project_id", $recovered_revision['project_id'])
            ->order_by("created_at", "DESC")
            ->limit(1)
            ->get()
            ->row_array();
        
        $error_message = null;
        
        if ($current_revision["content"] === $recovered_revision["content"]) {
            $error_message = 'Cannot recover this version since content is same as current';
            $this->output->set_output(json_encode([ "error" => "Cannot recover this version since content is same as current" ]));
            return false;
        } else {
            $data = [
                "content_id" => $recovered_revision["content_id"],
                "project_id" => $recovered_revision["project_id"],
                "content" => $recovered_revision["content"],
                "user_id" => $this->ion_auth->user()->row()->id,
            ];
            $this->db->insert("product_content_revisions", $data);
        }
        
        $this->output->set_output(json_encode([ "redirect" => "/editor/" . $recovered_revision["project_id"] . "/" . $this->product_model->getSectionId($recovered_revision["content_id"]) . "#p" . $recovered_revision["content_id"] ]));
    }
    
    private function _is_reviewer($project_id)
    {
        return $this->project_model->isReviewer($this->ion_auth->user()->row()->id, $project_id);
    }
    
    private function _is_manager($project_id)
    {
        return $this->project_model->isManager($this->ion_auth->user()->row()->id, $project_id);
    }
    
    private function _can_review($project_id)
    {
        return $this->_is_reviewer($project_id) || $this->_is_manager($project_id);
    }
    
    private function _can_commit($project_id)
    {
        return ! $this->_is_reviewer($project_id);
    }
    
    private function _can_always_commit($project_id)
    {
        return $this->_is_manager($project_id);
    }
    
    private function _can_auto_translate($project)
    {
        return ! $this->_is_reviewer($project["id"]) && $project["google_code"];
    }
}
