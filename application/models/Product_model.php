<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public $file_types = [
		"pdf_printing" => "PDF (Production)",
		"pdf_personal" => "PDF (Personal)",
		"indd" => "InDesign",
		
	];

	public function getProducts($filter = array()) {
		$products = $this->_getProductsQuery($filter)
			->limit($filter['per_page'], ($filter['page'] - 1) * $filter['per_page'])
			->get()
			->result_array();

		return array_map( function( $product ) {
			$product["languages"] = $this->db->select( "DISTINCT( language_id )" )
				->from( "product_attachments" )
				->where( "product_id", $product["id"] )
				->group_by( "language_id" )
				->get()
				->num_rows();
			return $product;
		}, $products );
	}

	public function getProductsCount($filter = array()) {
		return $this->_getProductsQuery($filter)->get()->num_rows();
	}

	public function getProduct( $product_id ) {
		$productArray = $this->db->select( "p.*, pb.name as binding_name" )
			->from( "products as p" )
			->join( "product_bindings as pb", "pb.id = p.binding")
			->where( "p.id", $product_id )
			->get()
			->row_array();

		$productArray["audience"] = [];
		$audiences = $this->getProductAudiences($product_id);
		foreach ($audiences as $item)
		{
			$productArray["audience"][] = $item['name'];
		}

		return $productArray;
	}
	
	public function getAttachments( $product_id ) {
		$attachments = $this->db->select( "*" )
			->from( "product_attachments" )
			->where( "product_id", $product_id )
			->join( "languages", "product_attachments.language_id = languages.id" )
			->get()
			->result_array();
		
		return array_map( function( $attachment ) {
			$attachment["file_type"] = $this->file_types[$attachment["file_type"]];
			return $attachment;
		}, $attachments );
	}
	
	public function getSection( $section_id ) {
		return $this->db->select( "*" )
			->from( "product_sections" )
			->where( "id", $section_id )
			->get()
			->row_array();
	}
	
	public function getSectionContent( $project_id, $section_id, $user_id = null ) {
		$content = $this->db->select( "*, product_content.id as id" )
			->from( "product_content" )
			->where( "section_id", $section_id )
			->where( "is_hidden", false )
			->join( "project_content_status", "project_content_status.content_id = product_content.id AND project_content_status.project_id = " . $project_id, "left" )
			->get()
			->result_array();
		
		return array_map( function( $content ) use( $project_id, $user_id ) {
			$revisions = $this->db->select( "*, product_content_revisions.id" )
				->from( "product_content_revisions" )
				->where( "content_id", $content["id"] )
				->where( "project_id", $project_id )
				->order_by( "created_at", "desc" )
				->join( "users", "product_content_revisions.user_id = users.id" )
				->get()
				->result_array();
			
			foreach( $revisions as $key => $revision ) {
				$date = new DateTime( $revision["created_at"] );
				$revisions[$key]["created_at_formatted"] = $date->format( "Y-m-d H:i a" );
				$revisions[$key]["created_at"] = $date->format( "c" );
				$old_content = array_key_exists( $key+1, $revisions ) ? $revisions[$key+1]["content"] : "";
				$revisions[$key]["diff"] = $this->_htmlDiff( $old_content, $revision["content"] );
			}
			
			$approvals = $this->db->select( "*" )
				->from( "project_content_approval" )
				->where( "content_id", $content["id"] )
				->where( "project_id", $project_id )
				->join( "users", "project_content_approval.approved_by = users.id" )
				->get()
				->result_array();
			
			$content["revisions"] = $revisions;
			$content["total_revisions"] = count( $content["revisions"] );
			$content["latest_revision"] = $content["revisions"][0]["content"] ?? "";
			$content["approvals"] = $approvals;
			$content["total_approvals"] = count( $approvals );
			
			if( $user_id ) {
				$has_approved = $this->_user_has_approved_content($content["id"], $project_id, $user_id);
				$content["user_has_approved"] = $has_approved;
			}
			
			$errors = $this->db->select( "*" )
				->from( "product_content_log" )
				->where( "content_id", $content["id"] )
				->where( "project_id", $project_id )
				->where( "type", "error" )
				->where( "is_resolved", false )
				->order_by( "created_at", "desc" )
				->get()
				->result_array();	
			
			$content["errors"] = $errors;
			
			$str_length = strlen( $content["content"] );
			$content["textarea_height"] = $str_length > 60 ? ( $str_length / 60 ) + 1 : 2;
			return $content;
		}, $content );
	}
	
	public function getSectionId( $content_id ) {
		$section = $this->db->select( "section_id" )
			->from( "product_content" )
			->where( "id", $content_id )
			->get()
			->row_array();
		return $section["section_id"];
	}
	
	public function getContentLog( $id ) {
		return $this->db->select( "*" )
			->from( "product_content_log" )
			->where( "id", $id )
			->get()
			->row_array();
	}
	
	public function getSeriesItems() {
		return $this->db->select( "*" )
			->from( "series" )
			->get()
			->result_array();
	}

	public function getAudiencesList() {
		return $this->db->select( "*" )
			->from( "audiences" )
			->order_by( "id", "ASC" )
			->get()
			->result_array();
	}

	public function getUniqueProductNames() {
		return $this->db->select( "name" )
			->distinct()
			->from( "products" )
			->order_by( "name", "ASC" )
			->get()
			->result_array();
	}

	public function addProductAudiencesData($audiences = array(), $product_id) {
		if (empty($audiences)) {
			return;
		}
		foreach ($audiences as $item) {
			$data = array(
				'product_id' => $product_id,
				'audience_id' => $item
			);
			$this->db->insert('product_audiences', $data);
		}
	}

	public function updateProductAudiencesData($audiences = array(), $product_id) {
		$this->db->delete('product_audiences', array('product_id' => $product_id));
		foreach ($audiences as $item) {
			$data = array(
				'product_id' => $product_id,
				'audience_id' => $item
			);
			$this->db->insert('product_audiences', $data);
		}
	}

	public function getProductAudiences($product_id) {
		return $this->db->select( "a.id, a.name" )
			->from( "audiences a" )
			->join( "product_audiences pa", "pa.audience_id = a.id" )
			->where( "product_id", $product_id )
			->get()
			->result_array();
	}

	public function getUniqueAuthorNames() {
		return $this->db->select( "author" )
			->distinct()
			->from( "products" )
			->where( "author != ", "")
			->order_by( "author", "ASC" )
			->get()
			->result_array();
	}

	public function getProductBindingsList() {
		return $this->db->select( "*" )
			->from( "product_bindings" )
			->get()
			->result_array();
	}

	private function _user_has_approved_content( $content_id, $project_id, $user_id ) {
		return $this->db->select( "*" )
			->from( "project_content_approval" )
			->where( "content_id", $content_id )
			->where( "project_id", $project_id )
			->where( "approved_by", $user_id )
			->count_all_results() >= 1;
	}
	
	//https://github.com/paulgb/simplediff
	private function _diff($old, $new){
		$matrix = array();
		$maxlen = 0;
		foreach($old as $oindex => $ovalue){
			$nkeys = array_keys($new, $ovalue);
			foreach($nkeys as $nindex){
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
					$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
				if($matrix[$oindex][$nindex] > $maxlen){
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}   
		}
		if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
		return array_merge(
			$this->_diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			$this->_diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
	}
	private function _htmlDiff($old, $new){
		$ret = '';
		$diff = $this->_diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
		foreach($diff as $k){
			if(is_array($k))
				$ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
					(!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
			else $ret .= $k . ' ';
		}
		return $ret;
	}

	private function _getProductsQuery($filter = array())
	{
		$productsQuery = $this->db->select( "p.*" )
			->from( "products as p" );
		// filtering
		if (isset($filter['title']) && $filter['title'] != '') {
			$productsQuery = $productsQuery->where( "p.name", $filter['title'] );
		}
		if (isset($filter['available_in']) && $filter['available_in'] != '') {
			$productsQuery = $productsQuery->join( "projects", "projects.product_id = p.id" )
				->where( "projects.language_id", $filter['available_in'] );
		}
		if (isset($filter['audience']) && $filter['audience'] != '') {
			$productsQuery = $productsQuery->join( "product_audiences as pa", "pa.product_id = p.id" )
				->where( "pa.audience_id", $filter['audience'] );
		}
		if (isset($filter['author']) && $filter['author'] != '') {
			$productsQuery = $productsQuery->where( "author", $filter['author'] );
		}
		if (isset($filter['type']) && $filter['type'] != '') {
			$productsQuery = $productsQuery->where( "type", $filter['type'] );
		}
		if (isset($filter['binding']) && $filter['binding'] != '') {
			$productsQuery = $productsQuery->where( "binding", $filter['binding'] );
		}
		// sorting
		// default sorting option is a name of product
		if (!isset($filter['sort_by']) || $filter['sort_by'] == 'title') {
			$productsQuery = $productsQuery->order_by( "p.name", "ASC" );
		}
		if (isset($filter['sort_by']) && $filter['sort_by'] == 'author') {
			$productsQuery = $productsQuery->order_by( "p.author", "ASC" );
		}
		if (isset($filter['sort_by']) && $filter['sort_by'] == 'publisher') {
			$productsQuery = $productsQuery->order_by( "p.publisher", "ASC" );
		}

		return $productsQuery;
	}

}
