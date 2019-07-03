<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public $status = [
        0 => "In progress",
        1 => "Awaiting review",
        2 => "Approved",
        3 => "Finalized",
    ];
    
    public function getProjects($language_id = null)
    {
        if (is_numeric($language_id)) {
            $projects = $this->_projectsQuery()
                ->where("languages.id", $language_id)
                ->get()->result_array();
        } else {
            $projects = $this->_projectsQuery()->get()->result_array();
        }
        $total_strings = $this->_getTotalStringCountsByProduct();
        $completed_strings = $this->_getCompletedStringCountsByProject();
        return array_map(function ($project) use ($total_strings, $completed_strings) {
            $project["total_strings"] = $total_strings[$project["product_id"]] ?? 0;
            $project["completed_strings"] = $completed_strings[$project["id"]] ?? 0;
            $project["percent_complete"] = round($project["total_strings"] > 0 ? $project["completed_strings"] / $project["total_strings"] * 100 : 0, 0);
            $project["status"] = $this->status[$project["status"]];
            $members = $this->db->select("*")
                ->from("project_members")
                ->where("project_id", $project["id"])
                ->join("users", "project_members.user_id = users.id")
                ->get()
                ->result_array();
            $project["members"] = array_map(function ($member) {
                $member["avatar"] = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($member["email"]))) . "?s=72&d=mp";
                return $member;
            }, $members);
            return $project;
        }, $projects);
    }
    
    public function getProjectLanguages()
    {
        return $this->db->select("languages.id, languages.name")
            ->distinct()
            ->from("languages")
            ->join("projects", "projects.language_id = languages.id")
            ->get()
            ->result_array();
    }
    
    public function getProjectsByProductId($product_id)
    {
        $projects = $this->_projectsQuery()
            ->where("product_id", $product_id)
            ->get()
            ->result_array();
        return array_map(function ($project) {
            $project["total_strings"] = $this->_getTotalStringCount($project["product_id"]);
            $project["completed_strings"] = $this->_getCompletedStringCount($project["id"]);
            $project["percent_complete"] = round($project["total_strings"] > 0 ? $project["completed_strings"] / $project["total_strings"] * 100 : 0, 0);
            $project["status"] = $this->status[$project["status"]];
            return $project;
        }, $projects);
    }
    
    public function getProject($project_id)
    {
        $project = $this->db->select("projects.*, languages.name as language_name, languages.google_code as google_code, languages.rtl as text_rtl")
            ->from("projects")
            ->join("languages", "projects.language_id = languages.id")
            ->where("projects.id", $project_id)
            ->get()
            ->row_array();
        if (! $project) {
            show_404();
        }
        $project["total_strings"] = $this->_getTotalStringCount($project["product_id"]);
        $project["completed_strings"] = $this->_getCompletedStringCount($project_id);
        $project["percent_complete"] = round($project["total_strings"] > 0 ? $project["completed_strings"] / $project["total_strings"] * 100 : 0, 0);
        $project["status"] = $this->status[$project["status"]];
        return $project;
    }
    
    public function getMembers($project_id, $show_invited)
    {
        $query = $this->db->select("*")
            ->from("project_members")
            ->where("project_id", $project_id);
        if (! $show_invited) {
            $query->where("invite_email", null);
        }
        return $query->join("users", "project_members.user_id = users.id", "left")
            ->get()
            ->result_array();
    }
    
    public function getSections($project_id)
    {
        $sections = $this->db->select("*, product_sections.id as id")
            ->from("product_sections")
            ->join("projects", "product_sections.product_id = projects.product_id", "left")
            ->where("projects.id", $project_id)
            ->get()
            ->result_array();
        
        $project = $this->db->select("*")
            ->from("projects")
            ->where("id", $project_id)
            ->get()
            ->row_array();
        
        $total_strings = $this->_getTotalStringCountsBySection($project["product_id"]);
        $completed_strings = $this->_getCompletedStringCountsBySection($project_id);
        
        return array_map(function ($section) use ($total_strings, $completed_strings) {
            $section["total_strings"] = $total_strings[$section["id"]] ?? 0;
            $section["completed_strings"] = $completed_strings[$section["id"]] ?? 0;
            $section["percent_complete"] = round($section["total_strings"] > 0 ? $section["completed_strings"] / $section["total_strings"] * 100: 0, 0);
            $section["last_activity"] = "today";
            return $section;
        }, $sections);
        
        return $sections;
    }
    
    private function _getCompletedStringCountsByProject()
    {
        $strings = $this->db->select("project_id, COUNT(id) as count")
            ->where("is_approved", true)
            ->group_by("project_id")
            ->from("project_content_status")
            ->get()
            ->result_array();
        
        $array = [];
        foreach ($strings as $string) {
            $array[$string["project_id"]] = $string["count"];
        }
        return $array;
    }
    
    private function _getCompletedStringCountsBySection($project_id)
    {
        $strings = $this->db->select("product_content.section_id, COUNT(project_content_status.id) as count")
            ->where("project_content_status.project_id", $project_id)
            ->where("project_content_status.is_approved", true)
            ->join("product_content", "product_content.id = project_content_status.content_id")
            ->group_by("product_content.section_id")
            ->from("project_content_status")
            ->get()
            ->result_array();
        
        $array = [];
        foreach ($strings as $string) {
            $array[$string["section_id"]] = $string["count"];
        }
        return $array;
    }
    
    private function _getTotalStringCountsByProduct()
    {
        $strings = $this->db->select("product_id, COUNT(id) as count")
            ->group_by("product_id")
            ->from("product_content")
            ->get()
            ->result_array();
        
        $array = [];
        foreach ($strings as $string) {
            $array[$string["product_id"]] = $string["count"];
        }
        return $array;
    }
    
    private function _getTotalStringCountsBySection($product_id)
    {
        $strings = $this->db->select("section_id, COUNT(id) as count")
            ->where("product_id", $product_id)
            ->group_by("section_id")
            ->from("product_content")
            ->get()
            ->result_array();
        
        $array = [];
        foreach ($strings as $string) {
            $array[$string["section_id"]] = $string["count"];
        }
        return $array;
    }
    
    private function _getTotalStringCount($product_id)
    {
        return $this->db->select("id")
            ->where("product_id", $product_id)
            ->from("product_content")
            ->count_all_results();
    }
    
    private function _getCompletedStringCount($project_id)
    {
        return $this->db->select("id")
            ->where("project_id", $project_id)
            ->where("is_approved", true)
            ->from("project_content_status")
            ->count_all_results();
    }
    
    public function getLanguages()
    {
        return $this->db->select("*")
            ->from("languages")
            ->get()
            ->result_array();
    }
    
    public function getLanguageName($language_id)
    {
        return $this->db->select("*")
            ->from("languages")
            ->where("id", $language_id)
            ->get()
            ->row_array();
    }
    
    public function getMembershipByUserId($user_id)
    {
        return $this->db->select("*, products.name as product_name, languages.name as language_name, project_members.type as member_type")
            ->from("project_members")
            ->where("user_id", $user_id)
            ->join("projects", "project_members.project_id = projects.id")
            ->join("products", "projects.product_id = products.id")
            ->join("languages", "projects.language_id = languages.id")
            ->get()
            ->result_array();
    }
    
    private function _projectsQuery()
    {
        return $this->db->select("projects.*, products.name as product_name, languages.name as language_name")
            ->from("projects")
            ->join("products", "projects.product_id = products.id")
            ->join("languages", "projects.language_id = languages.id");
    }
    
    public function isReviewer($user_id, $project_id)
    {
        $count_existing = $this->db->select("id")
            ->where("user_id", $user_id)
            ->where("project_id", $project_id)
            ->where("type", "reviewer")
            ->from("project_members")
            ->count_all_results();
        
        return $count_existing > 0;
    }
    
    public function isManager($user_id, $project_id)
    {
        $count_existing = $this->db->select("id")
            ->where("user_id", $user_id)
            ->where("project_id", $project_id)
            ->where("type", "manager")
            ->from("project_members")
            ->count_all_results();
        
        return $count_existing > 0;
    }
    
    public function updateContentStatus($content_id, $project_id, $is_approved)
    {
        $project_content = $this->db->select("*")
            ->from("project_content_status")
            ->where("content_id", $content_id)
            ->where("project_id", $project_id)
            ->get()
            ->row_array();
        
        $data = [
            "content_id" => $content_id,
            "project_id" => $project_id,
            "is_approved" => $is_approved,
        ];
        
        if ($project_content) {
            $this->db->where("id", $project_content["id"]);
            $this->db->update("project_content_status", $data);
        } else {
            $this->db->insert("project_content_status", $data);
        }
    }
    
    public function getContentApprovals($content_id, $project_id)
    {
        return $this->db->select("*")
            ->from("project_content_approval")
            ->where("content_id", $content_id)
            ->where("project_id", $project_id)
            ->get()
            ->result_array();
    }
    
    public function removeContentApprovals($content_id, $project_id)
    {
        $this->db->where("content_id", $content_id)
            ->where("project_id", $project_id)
            ->delete("project_content_approval");
    }
    
    public function addContentApproval($content_id, $project_id, $user_id)
    {
        $approval_exists = $this->db->select("*")
            ->from("project_content_approval")
            ->where("content_id", $content_id)
            ->where("project_id", $project_id)
            ->where("approved_by", $user_id)
            ->count_all_results();
        
        if (! $approval_exists) {
            $data = [
                "content_id" => $content_id,
                "project_id" => $project_id,
                "approved_by" => $user_id,
            ];

            $this->db->insert("project_content_approval", $data);
        }
    }
    
    public function addMember($user_id, $project_id, $type = "translator")
    {
        $data = [
            "user_id" => $user_id,
            "project_id" => $project_id,
            "type" => $type,
        ];
        
        $this->db->where("user_id", $user_id)
            ->where("project_id", $project_id)
            ->delete("project_members");
        
        $this->db->insert("project_members", $data);
        return $this->db->insert_id();
    }
    
    public function getContent($id)
    {
        return $this->db->select("*")
            ->from("product_content")
            ->where("id", $id)
            ->get()
            ->row_array();
    }


	public function getUserActivity( $user_id, $time ) {
		$projects = [];
		$revised_content = $this->_activityQuery( "product_content_revisions", $time, $user_id );
		$activity = $this->_activityQuery( "product_content_log", $time, $user_id );

		foreach( $revised_content as $content ) {
			$project_id = $content["project_id"];
			$content_id = $content["content_id"];
			$user_id = $content["user_id"];

			if( ! isset( $projects[$project_id] ) ) {
				$projects[$project_id]["name"] = $content["project_name"];
			}
			if( ! isset( $projects[$project_id]["user_activity"][$user_id] ) ) {
				$projects[$project_id]["user_activity"][$user_id] = [
					"name" => $content["user_name"],
					"revisions" => 1,
				];
			} else {
				$projects[$project_id]["user_activity"][$user_id]["revisions"]++;
			}
		}

		foreach( $activity as $content ) {
			$project_id = $content["project_id"];
			$content_id = $content["content_id"];
			$user_id = $content["user_id"];
			$type = $content["type"];

			if( ! isset( $projects[$project_id] ) ) {
				$projects[$project_id]["name"] = $content["project_name"];
			}
			if( ! isset( $projects[$project_id]["user_activity"][$user_id] ) ) {
				$projects[$project_id]["user_activity"][$user_id]["name"] = $content["user_name"];
			}
			if( ! isset( $projects[$project_id]["user_activity"][$user_id][$type] ) ) {
				$projects[$project_id]["user_activity"][$user_id][$type] = 1;
			} else {
				$projects[$project_id]["user_activity"][$user_id][$type]++;
			}
		}
		return $projects;
	}
	
	private function _activityQuery( $table, $date, $user_id ) {
		$group_by = [ "user_id", "content_id", "section_id", "project_id" ];
		$extra_select = "";
		if( $table == "product_content_log" ) {
			$extra_select = "activity_table.type as type";
			array_push( $group_by, "type" );
		}
		return $this->db->select( "activity_table.content_id, activity_table.user_id, activity_table.project_id, CONCAT(products.name, ' (', languages.name, ')') as project_name, CONCAT(users.first_name, ' ', users.last_name) as user_name, $extra_select" )
			->from( "$table as activity_table" )
			->group_by( $group_by )
			->join( "product_content", "activity_table.content_id = product_content.id" )
			->join( "products", "product_content.product_id = products.id" )
			->join( "projects", "activity_table.project_id = projects.id" )
			->join( "languages", "projects.language_id = languages.id" )
			->join( "users", "activity_table.user_id = users.id" )
			->join( "project_members", "projects.id = project_members.project_id" )
			->where( "created_at >=", $date )
			->where( "project_members.user_id", $user_id )
			->where( "activity_table.user_id !=", $user_id )
			->get()
			->result_array();
	}
}
