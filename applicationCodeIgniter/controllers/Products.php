<?php
defined("BASEPATH") or exit("No direct script access allowed");

use AdventistCommons\Import\Idml\IDMLfile;
use AdventistCommons\Import\Idml\IDMLlib;
use AdventistCommons\Import\Idml\IDMLextend;

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library([ "ion_auth", "form_validation", "upload", "twig", "container" ]);
        $this->load->model("product_model");
        $this->load->helper('url');
        $user = $this->ion_auth->user()->row();
        if ($user) {
            $user->image = md5(strtolower(trim($this->ion_auth->user()->row()->email)));
            $user->is_admin = $this->ion_auth->is_admin();
            $this->twig->addGlobal("user", $user);
        }
    }

    public $product_types = [
        "book",
        "magabook",
        "booklet",
        "tract",
    ];

    public $breadcrumbs = [
        [
            "label" => "Products",
            "url" => "/products",
        ],
    ];
    
    public function index($product_id = null)
    {
        $this->load->model("project_model");
        $this->load->library("pagination");

        $audiences_options = $this->product_model->getAudiencesList();
        $product_bindings =  $this->product_model->getProductBindingsList();
        $title_options = $this->product_model->getUniqueProductNames();
        $available_in_options = $this->project_model->getProjectLanguages();
        $author_options = $this->product_model->getUniqueAuthorNames();

        $this->form_validation->set_rules('title', 'Title', 'callback__product_title_check[' . json_encode($title_options) . ']');
        $this->form_validation->set_rules('available_in', 'Available In', 'callback__product_language_check[' . json_encode($available_in_options) . ']');
        $this->form_validation->set_rules('audience', 'Audience', 'callback__product_audience_check[' . json_encode($audiences_options) . ']');
        $this->form_validation->set_rules('author', 'Author', 'callback__product_author_check[' . json_encode($author_options) . ']');
        $this->form_validation->set_rules('type', 'Type', 'callback__product_type_check');
        $this->form_validation->set_rules('binding', 'Binding', 'callback__product_binding_check[' . json_encode($product_bindings) . ']');
        $this->form_validation->set_rules('page', 'Page number', 'required|is_natural_no_zero');

        if ($this->input->method(true) == 'POST' && $this->form_validation->run() == false) {
            show_404();
        }

        $filter_data = $this->input->post();
        $filter_data['page'] = !$this->input->post('page') ? 1: $this->input->post('page');
        $filter_data['per_page'] = $this->config->item('per_page');

        if ($this->input->get('p') && $this->input->get('p') != $filter_data['page']) {
            show_404();
        }

        $pagination_config = array();
        $pagination_config["base_url"] = base_url() . "products";
        $pagination_config["total_rows"] = $this->product_model->getProductsCount($filter_data);
        $this->pagination->initialize($pagination_config);

        $data = [
            "products" => $this->product_model->getProducts($filter_data),
            "audience_options" => $this->product_model->getAudiencesList(),
            "product_types" => $this->product_types,
            "product_bindings" => $this->product_model->getProductBindingsList(),
            "series" => $this->product_model->getSeriesItems(),
            "title_options" => $title_options,
            "available_in_options" => $available_in_options,
            "author_options" => $author_options,
            "filter" => $filter_data,
            "links" => $this->pagination->create_links()
        ];
        $this->breadcrumbs[] = [ "label" => "All"  ];
        $this->twig->addGlobal("title", "Products");
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/products.html", $data);
    }
    
    public function detail($product_id)
    {
        $this->load->model("project_model");
        $product = $this->product_model->getProduct($product_id);
        if (! $product) {
            show_404();
        }
        $attachments = $this->product_model->getAttachments($product_id);
        $languages = [];
        foreach ($attachments as $attachment) {
            $languages[$attachment["language_id"]]["language_name"] = $attachment["name"];
            $languages[$attachment["language_id"]]["attachments"][] = $attachment;
        }
        $projects = $this->project_model->getProjectsByProductId($product_id);
        foreach ($projects as $project) {
            if (! array_key_exists($project["language_id"], $languages)) {
                $languages[$project["language_id"]]["language_name"] = $project["language_name"];
                $languages[$project["language_id"]]["project"] = $project;
            }
        }
        $data = [
            "product" => $product,
            "languages" => $languages,
            "file_types" => $this->product_model->file_types,
        ];
        $this->breadcrumbs[] = [ "label" => $product["name"]  ];
        $this->twig->addGlobal("title", $product["name"]);
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/product.html", $data);
    }
    
    public function edit($product_id)
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }

        $product = $this->product_model->getProduct($product_id);
        if (! $product) {
            show_404();
        }

        $data = [
            "product" => $product,
            "audience_options" => $this->product_model->getAudiencesList(),
            "product_types" => $this->product_types,
            "product_binding" => $this->product_model->getProductBindingsList(),
            "series" => $this->product_model->getSeriesItems(),
        ];
        $this->breadcrumbs[] = [
            "label" => $product["name"],
            "url" => "/products/" . $product["id"],
        ];
        $this->breadcrumbs[] = [
            "label" => "Edit",
        ];
        $this->twig->addGlobal("title", "Edit Product");
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/edit_product.html", $data);
    }
    
    public function validateIdml()
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }
        $this->output->set_content_type("application/json");

        if (!$_FILES["idml_file"]["name"]) {
            $this->output->set_output(json_encode([ "error" => 'No file given' ]));
            return false;
        }
        $idml_file = $this->_uploadIdml();
        if (! $idml_file) {
            $this->output->set_output(json_encode([ "error" => $this->upload->display_errors() ]));
            return false;
        }
        /** @var \AdventistCommons\Idml\Builder $idmlBuilder */
        $idmlBuilder = $this->container->get(\AdventistCommons\Idml\Builder::class);
        try {
            /** @var \AdventistCommons\Idml\Entity\Holder $holder */
            $holder = $idmlBuilder->buildFromPath($idml_file['full_path']);
            $holder->validate();
        } catch (\AdventistCommons\Idml\DomManipulation\Exception $e) {
            $this->output->set_output(json_encode([
                "error" => '<strong>Idml is not valid</strong><br />'.nl2br($e->getMessage())
            ]));
            return false;
        }

        $this->output->set_output(json_encode([ "success" => "Idml looks just fine" ]));
    }
    
    public function save()
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }
        
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("name", "Title", "required");
        $this->form_validation->set_rules("page_count", "Page count", "required|numeric");
        $this->form_validation->set_rules("type", "Product type", "required");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        $data = $this->input->post();
        
        $is_new = ! array_key_exists("id", $data);
        
        if ((array_key_exists("id", $data) && $_FILES["cover_image"]["name"]) || $is_new) {
            $cover_image = $this->_uploadCoverImage();
            if (! $cover_image) {
                $this->output->set_output(json_encode([ "error" => $this->imageUploadError ?? "Error uploading cover image" ]));
                return false;
            }
            $data["cover_image"] = $cover_image["file_name"];
        }
        
        $idml_file = null;
        if ($is_new && $_FILES["idml_file"]["name"]) {
            $idml_file = $this->_uploadIdml();
            if (! $idml_file) {
                $this->output->set_output(json_encode([ "error" => "Error uploading translation file" ]));
                return false;
            }
            $data["idml_file"] = $idml_file["raw_name"];
        }
        
        if ($data["series_id"] == "") {
            $data["series_id"] = null;
        } elseif (! is_numeric($data["series_id"])) {
            $this->db->insert("series", [ "name" => $data["series_id"] ]);
            $data["series_id"] = $this->db->insert_id();
        }

        $audience = array();
        if (isset($data['audience'])) {
            $audience = $data['audience'];
            unset($data['audience']);
        }

        $audience = array();
        if (isset($data['audience'])) {
            $audience = $data['audience'];
            unset($data['audience']);
        }
        if ($is_new) {
            if (isset($data['idml_file'])) {
                /** @var \AdventistCommons\Idml\Builder $idmlBuilder */
                $idmlBuilder = $this->container->get(\AdventistCommons\Idml\Builder::class);
                $idmlPath = self::getUploadDir() . $data['idml_file'] . '.idml';
                try {
                    /** @var \AdventistCommons\Idml\Holder $holder */
                    $holder = $idmlBuilder->buildFromProductAndPath($data, $idmlPath);
                    $holder->validate();
                } catch (\AdventistCommons\Idml\DomManipulation\Exception $e) {
                    $this->output->set_output(json_encode([ "error" => $e->getMessage() ]));
                    return false;
                }
            }
            $this->db->insert("products", $data);

            $id = $this->db->insert_id();
            $data['id'] = $id;
            if (isset($data['idml_file'])) {
                /** @var \AdventistCommons\Idml\Importer $idmlImporter */
                $idmlImporter = $this->container->get(\AdventistCommons\Idml\Importer::class);
                try {
                    /** @var \AdventistCommons\Idml\Entity\Holder $holder */
                    $idmlImporter->import($holder, $id);
                } catch (\AdventistCommons\Idml\DomManipulation\Exception $e) {
                    $this->output->set_output(json_encode([ "error" => $e->getMessage() ]));
                    return false;
                }
            }
            
            $this->output->set_output(json_encode(["redirect" => "/products/$id"]));
        } else {
            $this->db->where("id", $data["id"]);
            $this->db->update("products", $data);
            $this->product_model->updateProductAudiencesData($audience, $data["id"]);
            if ($_FILES["cover_image"]["name"]) {
                $this->output->set_output(json_encode([ "redirect" => "/products/edit/" . $data["id"] ]));
            } else {
                $this->output->set_output(json_encode([ "success" => "Product info updated" ]));
            }
        }
    }

    public function save_idml()
    {
        if (!$this->ion_auth->is_admin()) {
            show_404();
        }

        $this->output->set_content_type("application/json");

        $idml_file = $this->_uploadIdml();
        if (!$idml_file) {
            $this->output->set_output(json_encode(["error" => "Error uploading translation file"]));
            return false;
        }

        $data = $this->input->post();
        $data["idml_file"] = $idml_file["file_name"];
        $this->db->where("id", $data["id"]);
        $this->db->update("products", $data);
        $this->output->set_output(json_encode(["redirect" => "/products/edit/" . $data["id"] . "#advanced"]));
    }
    
    public function save_specs()
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }
        
        $this->output->set_content_type("application/json");
        $data = $this->input->post();
        $this->db->where("id", $data["id"]);
        $this->db->update("products", $data);
        $this->output->set_output(json_encode([ "success" => "Product info updated" ]));
    }
    
    public function add_file()
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }
        
        $this->output->set_content_type("application/json");
        
        $this->form_validation->set_rules("language_id", "Language", "required");
        $this->form_validation->set_rules("product_id", "Product ID", "required");
        $this->form_validation->set_rules("file_type", "File type", "required");
        
        if ($this->form_validation->run() === false) {
            $this->output->set_output(json_encode([ "error" => validation_errors() ]));
            return false;
        }
        $data = $this->input->post();
        
        $attachment = $this->_uploadAttachment();
        if (! $attachment) {
            $this->output->set_output(json_encode([ "error" => "Error uploading file" ]));
            return false;
        }
        
        $data["file"] = $attachment["file_name"];
        
        $this->db->insert("product_attachments", $data);
        $id = $this->db->insert_id();
        $this->output->set_output(json_encode([ "redirect" => "/products/" . $data["product_id"] ]));
    }
    
    public function delete($product_id)
    {
        if (! $this->ion_auth->is_admin()) {
            show_404();
        }
        
        $this->db->where("id", $product_id)
            ->delete("products");
        
        redirect("/products", "refresh");
    }

    /**
     * Validate product type form parameter
     * @param string $val
     * @return boolean
     */
    public function _product_type_check($val)
    {
        if ($val == '') {
            return true;
        }
        if (!in_array($val, $this->product_types)) {
            return false;
        }

        return true;
    }
    
    public function _product_title_check($val, $options)
    {
        if ($val == '') {
            return true;
        }
        $data = json_decode($options);
        $res = false;
        foreach ($data as $item) {
            if ($item->name == $val) {
                $res = true;
                break;
            }
        }

        return $res;
    }
    
    public function _product_language_check($val, $options)
    {
        if ($val == '') {
            return true;
        }
        $data = json_decode($options);
        $res = false;
        foreach ($data as $item) {
            if ($item->id == $val) {
                $res = true;
                break;
            }
        }

        return $res;
    }
        
    public function _product_audience_check($val, $options)
    {
        if ($val == '') {
            return true;
        }
        $data = json_decode($options);
        $res = false;
        foreach ($data as $item) {
            if ($item->id == $val) {
                $res = true;
                break;
            }
        }

        return $res;
    }

    public function _product_author_check($val, $options)
    {
        if ($val == '') {
            return true;
        }
        $data = json_decode($options);
        $res = false;
        foreach ($data as $item) {
            if ($item->author == $val) {
                $res = true;
                break;
            }
        }

        return $res;
    }

    public function _product_binding_check($val, $options)
    {
        if ($val == '') {
            return true;
        }
        $data = json_decode($options);
        $res = false;
        foreach ($data as $item) {
            if ($item->id == $val) {
                $res = true;
                break;
            }
        }

        return $res;
    }

    public function download_idml($product_id)
    {
        $product = $this->product_model->getProduct($product_id);
        if (!$product) {
            show_404();
        }

        /** @var \AdventistCommons\Export\Idml\Builder $builder */
        $builder = $this->container->get(\AdventistCommons\Export\Idml\Builder::class);
        /** @var \AdventistCommons\Export\Idml\Idml $idml */
        $idml = $builder->buildFromArrayProduct($product);

        $this->load->helper('download');
        force_download(
            $idml->buildFileName(),
            $idml->getZipContent()
        );
    }

    private function _uploadCoverImage()
    {
        $config["upload_path"] = self::getUploadDir();
        $config["allowed_types"] = "jpg|jpeg|png";
        $config["max_size"] = 10000;
        $config["encrypt_name"] = true;
        $this->load->library("upload", $config);
        $this->upload->initialize($config);
        if (! $this->upload->do_upload("cover_image")) {
            $this->imageUploadError = $this->upload->display_errors();
            return false;
        }
        $image = $this->upload->data();
        $source_path = self::getUploadDir() . $image["file_name"];
        
        $config_manip = [
            "image_library" => "gd2",
            "source_image" => $source_path,
            "maintain_ratio" => true,
            "width" => 768,
            "height" => 768,
            "quality" => "70%",
        ];
        $this->load->library("image_lib", $config_manip);
        if (! $this->image_lib->resize()) {
            $this->imageUploadError = 'Cannot resize uploaded cover image file. May image library GD2 is missing';
            return false;
        }
        $this->image_lib->clear();
        return $this->upload->data();
    }

    private function _uploadIdml()
    {
        $config["upload_path"] = self::getUploadDir();
        $config["allowed_types"] = "idml";
        $config["max_size"] = 50000;
        $config["encrypt_name"] = true;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload("idml_file")) {
            return false;
        }

        return $this->upload->data();
    }

    private function _uploadAttachment()
    {
        $config["upload_path"] = self::getUploadDir();
        $config["allowed_types"] = "pdf";
        $config["max_size"] = 50000;
        $config["encrypt_name"] = true;
        $this->upload->initialize($config);
        if (! $this->upload->do_upload("file")) {
            return false;
        }
        
        return $this->upload->data();
    }
    
    private static function getUploadDir()
    {
        return realpath($_SERVER["DOCUMENT_ROOT"]. '/../uploads/');
    }
}
