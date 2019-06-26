<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model( "project_model" );
	}
	
	public function send_email_digest()
	{
		if( ! is_cli() ) {
			show_404();
		}
		
		$time = ( new DateTime() )->modify( "-1 day" )->format( "Y-m-d G:i:s" );
		
		$users = $this->db->select( "*" )
			->from( "users" )
			->where( "digest_email_processed_at <=", $time )
			->or_where( "digest_email_processed_at =", null )
			->limit( 50 )
			->get()
			->result_array();
		
		foreach( $users as $user ) {
			$projects = [];
			$last_processed = $user["digest_email_processed_at"] ?? $time;
			$revised_content = $this->_activityQuery( "product_content_revisions", $last_processed, $user["id"] );
			$activity = $this->_activityQuery( "product_content_log", $last_processed, $user["id"] );
			
			if( ! $revised_content ) {
				$this->_updateDigestTimestamp( $user );
				continue;
			}
			
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
			
			$data = [
				"projects" => $projects,
				"user" => $user,
			];
			
			$this->template->set( "heading", "Latest updates" );
			$content = $this->template->load( "email/template", "email/digest", $data, true );
			$this->email->from( "info@adventistcommons.org", "Adventist Commons" );
			$this->email->to( $user["email"] );
			$this->email->message( $content );
			$this->email->subject( "Latest updates" );
			$this->email->send();
			$this->_updateDigestTimestamp( $user );
		}
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
			->where( "created_at >=", $date )
			->where( "user_id", $user_id )
			->get()
			->result_array();
	}
	
	private function _updateDigestTimestamp( $user ) {
		$time = ( new DateTime() )->format( "Y-m-d G:i:s" );
		$this->db->where( "id", $user["id"] );
		$this->db->update( "users", [ "digest_email_processed_at" => $time ] );
	}
}
