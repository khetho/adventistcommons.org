<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public $breadcrumbs = [
        [
            "label" => "Home",
            "url" => "/",
        ],
    ];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library([ "ion_auth", "twig" ]);
        $this->load->model("product_model");
        $user = $this->ion_auth->user()->row();
        if ($user) {
            $user->image = md5(strtolower(trim($this->ion_auth->user()->row()->email)));
            $user->is_admin = $this->ion_auth->is_admin();
            $this->twig->addGlobal("user", $user);
        }
    }
    
    public function index()
    {
        $this->twig->addGlobal("title", "Certified Adventist Resources, Culturally Relevant");
        $this->twig->addGlobal("is_home", true);
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $data = [
            "message" => $this->session->flashdata('message'),
        ];
        $this->twig->display("twigs/home.html", $data);
    }
    
    public function feedback()
    {
        $this->breadcrumbs[] = [ "label" => "Feedback" ];
        $this->twig->addGlobal("title", "Feedback");
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $data = [
            "http_referer" => $_SERVER["HTTP_REFERER"],
        ];
        $this->twig->display("twigs/feedback.html", $data);
    }
    
    public function send_feedback()
    {
        $this->output->set_content_type("application/json");
        $name = $this->input->post("name");
        $email = $this->input->post("email");
        $message = $this->input->post("message");
        $referer = $this->input->post("referer");
        $user_id = $this->input->post("user_id");
        $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "";
        $ip = $_SERVER["REMOTE_ADDR"] ?? "";
        
        $message = <<<EOT
			<b>Name</b>: {$name}<br/>
			<b>Email</b>: {$email}<br/>
			<b>Message</b>: {$message}<br/><br/>
			<b>User ID</b>: {$user_id}<br/>
			<b>User agent</b>: {$user_agent}<br/>
			<b>Referal page</b>: {$referer}<br/>
			<b>IP address</b>: {$ip}<br/>
EOT;
        
        $this->email->from("info@adventistcommons.org", "Adventist Commons");
        $this->email->to("info@adventistcommons.org");
        $this->email->subject(sprintf("Adventist Commons feedback from %s", $name));
        $this->email->message($message);
        $this->email->send();
        $this->output->set_output(json_encode([ "success" => "Thanks! Feedback has been sent.", "redirect" => "/", "redirect_delay" => 2000 ]));
    }
}
