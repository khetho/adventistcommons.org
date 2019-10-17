<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library( [ "ion_auth", "form_validation", "twig", "container" ] );
		$this->load->helper( [ "url" ] );
		$this->load->model( "project_model" );
		$user = $this->ion_auth->user()->row();
		if( $user ) {
			$user->image = md5( strtolower( trim( $this->ion_auth->user()->row()->email ) ) );
			$user->is_admin = $this->ion_auth->is_admin();
			$this->twig->addGlobal( "user",  $user );
		}
	}
	
	public $breadcrumbs = [
		[
			"label" => "Translations",
			"url" => "/projects",
		],
	];
	
	public function index( $project_id = null )
	{
		$language_id = $_GET["language"] ?? null;
		$language = $this->project_model->getLanguageName( $language_id );
		$data = [
			"projects" => $this->project_model->getProjects( $language_id ),
			"languages" => $this->project_model->getProjectLanguages(),
			"selected_language" => $language,
		];
		
		$this->breadcrumbs[] = [ "label" => "All"  ];
		
		$this->twig->addGlobal( "title", "Dashboard" );
		$this->twig->addGlobal( "breadcrumbs", $this->breadcrumbs );
		$this->twig->display( "twigs/projects", $data );
	}
	
	public function detail( $project_id ) {
		$this->load->model( "product_model" );
		$project = $this->project_model->getProject( $project_id );
		$product = $this->product_model->getProduct( $project["product_id"] );
		$title = "{$product['name']} ({$project['language_name']})";
		$can_manage_members = $this->_can_manage_members( $project["id"] );
		$members = array_map( function( $member ) {
			$member["avatar"] = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $member["email"] ?? $member["invite_email"] ) ) ) . "?s=72&d=mp";
			return $member;
		}, $this->project_model->getMembers( $project["id"], $can_manage_members ) );
		
		$data = [
			"project" => $project,
			"product" => $product,
			"members" => $members,
			"sections" => $this->project_model->getSections( $project["id"] ),
			"can_manage_members" => $can_manage_members,
		];
		$this->twig->addGlobal( "title", $title );
		$this->twig->addGlobal( "breadcrumbs", $this->breadcrumbs );
		$this->twig->display( "twigs/project", $data );
	}
	
	public function get_languages() {
		$languages = $this->project_model->getLanguages();
		$this->output->set_content_type( "application/json" );
		$this->output->set_output( json_encode( $languages ) );
	}
	
	public function add( $product_id = null ) {
		if( ! $this->ion_auth->logged_in() ) {
			show_404();
		}
		$this->load->model( "product_model" );
		$this->output->set_content_type("application/json");
		$this->form_validation->set_rules( "language_id", "Language", "required" );
		$this->form_validation->set_rules( "product_id", "Product ID", "required" );
		
		if( $this->form_validation->run() === false ) {
			$this->output->set_output( json_encode( [ "error" => validation_errors() ] ) );
			return false;
		}
		
		$data = $this->input->post();
		if( ! $this->product_model->getProduct( $data["product_id"] ) ) {
			show_404();
		}
		
		$count_existing = $this->db->select( "id" )
			->where( "product_id", $data["product_id"] )
			->where( "language_id", $data["language_id"] )
			->from( "projects" )
			->count_all_results();
		
		if( $count_existing > 0 ) {
			$this->output->set_output( json_encode( [ "error" => "This product is already being translated into the chosen language" ] ) );
			return false;
		}
		
		$this->db->insert( "projects", $data );
		$id = $this->db->insert_id();
		$this->output->set_output( json_encode( [ "redirect" => "/projects/$id" ] ) );
	}
	
	public function add_member() {
		$this->output->set_content_type( "application/json" );
		$this->form_validation->set_rules( "type", "Member type", "required" );
		$this->form_validation->set_rules( "project_id", "Project ID", "required" );
		
		if( $this->form_validation->run() === false ) {
			$this->output->set_output( json_encode( [ "error" => validation_errors() ] ) );
			return false;
		}
		
		$data = $this->input->post();
		
		if( ! $this->_can_manage_members( $data["project_id"] ) || ( "type" == "manager" && ! $this->ion_auth->is_admin() ) ) {
			show_404();
		}
		
		$is_invite = isset( $data["invite_email"] );
		
		if( $is_invite ) {
			$insert_data = [
				"invite_email" => $data["invite_email"],
				"project_id" => $data["project_id"],
				"type" => $data["type"],
			];

			$this->db->where( "invite_email", $data["invite_email"] )
				->where( "project_id", $data["project_id"] )
				->delete( "project_members" );

			$this->db->insert( "project_members", $insert_data );
			$id = $this->db->insert_id();
		} else {
			$id = $this->project_model->addMember( $data["user_id"], $data["project_id"], $data["type"] );
		}
		
		$user = $this->ion_auth->user()->row( $data["user_id"] );
		
		$project = $this->db->select( "projects.*, languages.name as language_name, products.name as product_name" )
			->from( "projects" )
			->join( "languages", "projects.language_id = languages.id" )
			->join( "products", "projects.product_id = products.id" )
			->where( "projects.id", $data["project_id"] )
			->get()
			->row_array();
		
		$template_data = [
			"user" => $user->first_name,
			"project_name" => $project["product_name"] . " (" . $project["language_name"]. ")",
			"link" => base_url() . "projects/" . $project["id"],
			"type" => $data["type"],
		];
		
		$this->twig->addGlobal( "heading", "You've been invited to a new project!" );
		$this->twig->addGlobal( "base_url", base_url() );
		$content = $this->twig->render( $is_invite ? "twigs/email/invited_contributor" : "twigs/email/added_contributor", $template_data );
		$this->email->from( "info@adventistcommons.org", "Adventist Commons" );
		$this->email->to( $user->email );
		$this->email->message( $content );
		$this->email->subject( "You've been added as a " . $template_data["type"] . " to " . $template_data["project_name"] );
		$this->email->send();
		
		$this->output->set_output( json_encode( [ "member_id" => $id ] ) );
	}
	
	public function delete( $project_id ) {
		if( ! $this->ion_auth->is_admin() ) {
			show_404();
		}
		
		$this->db->where( "id", $project_id )
			->delete( "projects" );
		
		redirect( "/projects", "refresh" );
	}
	
	public function download_idml( $project_id )
	{
		$project = $this->project_model->getProject( $project_id );
		if (!$project) {
			show_404();
		}
		$this->load->model( "product_model" );
		$product = $this->product_model->getProduct( $project["product_id"] );
		
		/** @var \AdventistCommons\Export\Idml\Builder $builder */
		$builder = $this->container->get(\AdventistCommons\Export\Idml\Builder::class);
		/** @var \AdventistCommons\Export\Idml\Holder $idmlHolder */
		$idmlHolder = $builder->buildFromArrayProductAndProject($product, $project);
		
		$this->load->helper('download');
		force_download(
			$idmlHolder->buildFileName(),
			$idmlHolder->getZipContent()
		);
	}
	
	private function _can_manage_members( $project_id ) {
		return $this->ion_auth->logged_in() && $this->project_model->isManager( $this->ion_auth->user()->row()->id, $project_id ) || $this->ion_auth->is_admin();
	}
}
