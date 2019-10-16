<?php
defined("BASEPATH") OR exit("No direct script access allowed");

use AdventistCommons\Import\Idml\IDMLfile;
use AdventistCommons\Import\Idml\IDMLlib;
use AdventistCommons\Import\Idml\IDMLextend;

class Products extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library( [ "ion_auth", "form_validation", "upload", "twig", "container" ] );
		$this->load->helper( "url" );
		$this->load->model( "product_model" );
		$this->data = new stdClass();
		$this->load->helper('url');
		$user = $this->ion_auth->user()->row();
		if( $user ) {
			$user->image = md5( strtolower( trim( $this->ion_auth->user()->row()->email ) ) );
			$user->is_admin = $this->ion_auth->is_admin();
			$this->twig->addGlobal( "user",  $user );
		}
	}
	
	public $audience = [
		"Christian",
		"Muslim",
		"Buddhist",
		"Hindu",
		"Sikh",
		"Animist",
		"Secular",
	];
	
	public $product_types = [
		"book",
		"magabook",
		"booklet",
		"tract",
	];
	
	public $product_binding = [
		"Hardcover",
		"Perfect Bound",
		"Spiral Bound",
		"Saddle Stitch",
		"Folded",
	];
	
	public $breadcrumbs = [
		[
			"label" => "Products",
			"url" => "/products",
		],
	];
	
	public function index( $product_id = null )
	{
		$data = [
			"products" => $this->product_model->getProducts(),
			"audience_options" => $this->audience,
			"product_types" => $this->product_types,
			"product_binding" => $this->product_binding,
			"series" => $this->product_model->getSeriesItems(),
		];
		$this->breadcrumbs[] = [ "label" => "All"  ];
		$this->twig->addGlobal( "title", "Products" );
		$this->twig->addGlobal( "breadcrumbs", $this->breadcrumbs );
		$this->twig->display( "twigs/products", $data );
	}
	
	public function detail( $product_id ) {
		$this->load->model( "project_model" );
		$product = $this->product_model->getProduct( $product_id );
		if( ! $product ) show_404();
		$attachments = $this->product_model->getAttachments( $product_id );
		$languages = [];
		foreach( $attachments as $attachment ) {
			$languages[$attachment["language_id"]]["language_name"] = $attachment["name"];
			$languages[$attachment["language_id"]]["attachments"][] = $attachment;
		}
		$projects = $this->project_model->getProjectsByProductId( $product_id );
		foreach( $projects as $project ) {
			if( ! array_key_exists( $project["language_id"], $languages ) ) {
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
		$this->twig->addGlobal( "title", $product["name"] );
		$this->twig->addGlobal( "breadcrumbs", $this->breadcrumbs );
		$this->twig->display( "twigs/product", $data );
	}
	
	public function edit( $product_id ) {
		if( ! $this->ion_auth->is_admin() ) {
			show_404();
		}
		
		$product = $this->product_model->getProduct( $product_id );
		if( ! $product ) show_404();
		
		$data = [
			"product" => $product,
			"audience_options" => $this->audience,
			"product_types" => $this->product_types,
			"product_binding" => $this->product_binding,
			"series" => $this->product_model->getSeriesItems(),
		];
		$this->breadcrumbs[] = [
			"label" => $product["name"],
			"url" => "/products/" . $product["id"],
		];
		$this->breadcrumbs[] = [
			"label" => "Edit",
		];
		$this->twig->addGlobal( "title", "Edit Product" );
		$this->twig->addGlobal( "breadcrumbs", $this->breadcrumbs );
		$this->twig->display( "twigs/edit_product", $data );
	}
	
	public function save() {
		if( ! $this->ion_auth->is_admin() ) {
			show_404();
		}
		
		$this->output->set_content_type("application/json");
		
		$this->form_validation->set_rules( "name", "Title", "required" );
		$this->form_validation->set_rules( "page_count", "Page count", "required|numeric" );
		$this->form_validation->set_rules( "type", "Product type", "required" );
		
		if( $this->form_validation->run() === false ) {
			$this->output->set_output( json_encode( [ "error" => validation_errors() ] ) );
			return false;
		}
		$data = $this->input->post();
		
		$is_new = ! array_key_exists( "id", $data );
		
		if( ( array_key_exists( "id", $data ) && $_FILES["cover_image"]["name"] ) || $is_new ) {
			$cover_image = $this->_uploadCoverImage();
			if( ! $cover_image ) {
				$this->output->set_output( json_encode( [ "error" => $this->imageUploadError ?? "Error uploading cover image" ] ) );
				return false;
			}
			$data["cover_image"] = $cover_image["file_name"];
		}
		
		$idml_file = null;
		if( $is_new && $_FILES["idml_file"]["name"] ) {
			$idml_file = $this->_uploadIdml();
			if( ! $idml_file ) {
				$this->output->set_output( json_encode( [ "error" => "Error uploading translation file" ] ) );
				return false;
			}
			$data["idml_file"] = $idml_file["raw_name"];
		}
		
		if( $data["series_id"] == "" ) {
			$data["series_id"] = null;
		} elseif( ! is_numeric( $data["series_id"] ) ) {
			$this->db->insert( "series", [ "name" => $data["series_id"] ] );
			$data["series_id"] = $this->db->insert_id();
		}
		
		$data['audience'] = serialize($data['audience'] ?? []);
		if( $is_new ) {
			$this->db->insert("products", $data);

			$id = $this->db->insert_id();

			$param = array("uploads/" . $data['idml_file'] . ".idml");

			$file = new IDMLfile($param);

			$idml = new IDMLlib($file);

			$idmlExtend = $this->container->get(IDMLextend::class);

			$this->data->all_contents = $idml->getMyContent('Story');

			$idmlExtend->getSections($this->data->all_contents, $id);

			$idmlExtend->getProductContent($this->data->all_contents, $id);

			$idmlExtend->createSentence($id);

			$this->output->set_output(json_encode(["redirect" => "/products/$id"]));
		} else {
			$this->db->where( "id", $data["id"] );
			$this->db->update( "products", $data );
			if( $_FILES["cover_image"]["name"] ) {
				$this->output->set_output( json_encode( [ "redirect" => "/products/edit/" . $data["id"] ] ) );
			} else {
				$this->output->set_output( json_encode( [ "success" => "Product info updated" ] ) );
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
	
	public function save_specs() {
		if( ! $this->ion_auth->is_admin() ) {
			show_404();
		}
		
		$this->output->set_content_type("application/json");
		$data = $this->input->post();
		$this->db->where( "id", $data["id"] );
		$this->db->update( "products", $data );
		$this->output->set_output( json_encode( [ "success" => "Product info updated" ] ) );
	}
	
	public function add_file() {
		if( ! $this->ion_auth->is_admin() ) {
			show_404();
		}
		
		$this->output->set_content_type("application/json");
		
		$this->form_validation->set_rules( "language_id", "Language", "required" );
		$this->form_validation->set_rules( "product_id", "Product ID", "required" );
		$this->form_validation->set_rules( "file_type", "File type", "required" );
		
		if( $this->form_validation->run() === false ) {
			$this->output->set_output( json_encode( [ "error" => validation_errors() ] ) );
			return false;
		}
		$data = $this->input->post();
		
		$attachment = $this->_uploadAttachment();
		if( ! $attachment ) {
			$this->output->set_output( json_encode( [ "error" => "Error uploading file" ] ) );
			return false;
		}
		
		$data["file"] = $attachment["file_name"];
		
		$this->db->insert( "product_attachments", $data );
		$id = $this->db->insert_id();
		$this->output->set_output( json_encode( [ "redirect" => "/products/" . $data["product_id"] ] ) );
	}
	
	public function delete( $product_id ) {
		if( ! $this->ion_auth->is_admin() ) {
			show_404();
		}
		
		$this->db->where( "id", $product_id )
			->delete( "products" );
		
		redirect( "/products", "refresh" );
	}
	
	private function _uploadCoverImage() {
		$config["upload_path"] = $_SERVER["DOCUMENT_ROOT"] . "/uploads";
		$config["allowed_types"] = "jpg|jpeg|png";
		$config["max_size"] = 10000;
		$config["encrypt_name"] = true;
		$this->load->library( "upload", $config );
		$this->upload->initialize( $config );
		if ( ! $this->upload->do_upload( "cover_image" ) ) {
                       $this->imageUploadError = 'Cannot write uploaded cover image file';
			return false;
		}
		$image = $this->upload->data();
		$source_path = $_SERVER["DOCUMENT_ROOT"] . "/uploads/" . $image["file_name"];
		$target_path = $_SERVER["DOCUMENT_ROOT"] . "/uploads/";
		
		$config_manip = [
			"image_library" => "gd2",
			"source_image" => $source_path,
			"maintain_ratio" => true,
			"width" => 768,
			"height" => 768,
			"quality" => "70%",
		];
		$this->load->library( "image_lib", $config_manip );
		if( ! $this->image_lib->resize() ) {
			$this->imageUploadError = 'Cannot resize uploaded cover image file. May image library GD2 is missing';
			return false;
		}
		$this->image_lib->clear();
		return $this->upload->data();
	}

	private function _uploadIdml()
	{
		$config["upload_path"] = $_SERVER["DOCUMENT_ROOT"] . "/uploads";
		$config["allowed_types"] = "idml";
		$config["max_size"] = 50000;
		$config["encrypt_name"] = true;

		$this->upload->initialize($config);

		if (!$this->upload->do_upload("idml_file")) {
			return false;
		}
		$file = $this->upload->data();
		$this->_unzipIdml($file["file_name"], $file["raw_name"]);

		return $this->upload->data();
	}

	private function _unzipIdml($file_name, $raw_name)
	{
		$this->load->library("zip");
		$unzip_path = $_SERVER["DOCUMENT_ROOT"] . "/uploads/extracted/" . $raw_name;
		$zip = new ZipArchive();
		if ($zip->open($_SERVER["DOCUMENT_ROOT"] . "/uploads/" . $file_name)) {
			if (!$zip->extractTo($unzip_path)) {
				throw new Error("Unable to extract file");
			}
			$zip->close();
		} else {
			throw new Error("Unable to open file");
		}
	}

	private function _uploadAttachment() {
		$config["upload_path"] = $_SERVER["DOCUMENT_ROOT"] . "/uploads";
		$config["allowed_types"] = "pdf";
		$config["max_size"] = 50000;
		$config["encrypt_name"] = true;
		$this->upload->initialize( $config );
		if ( ! $this->upload->do_upload( "file" ) ) {
			return false;
		}
		
		$file = $this->upload->data();
		return $this->upload->data();
	}
}
