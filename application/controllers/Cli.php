<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function send_email_digest()
	{
		if( ! is_cli() ) {
			show_404();
		}
		
		$this->load->model( "project_model" );
		$time = ( new DateTime() )->modify( "-1 day" )->format( "Y-m-d G:i:s" );
		
		$users = $this->db->select( "*" )
			->from( "users" )
			->where( "digest_email_processed_at <=", $time )
			->or_where( "digest_email_processed_at =", null )
			->limit( 50 )
			->get()
			->result_array();
		
		foreach( $users as $user ) {
			$last_processed = $user["digest_email_processed_at"] ?? $time;
			$activity = $this->project_model->getUserActivity( $user["id"], $last_processed );
			
			if( ! $activity ) {
				$this->_updateDigestTimestamp( $user );
				continue;
			}
			$data = [
				"projects" => $activity,
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
	
	private function _updateDigestTimestamp( $user ) {
		$time = ( new DateTime() )->format( "Y-m-d G:i:s" );
		$this->db->where( "id", $user["id"] );
		$this->db->update( "users", [ "digest_email_processed_at" => $time ] );
	}
}
