<?php
defined('BASEPATH') or exit('No direct script access allowed');

class About extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library([ "twig" ]);
        $this->load->helper([ "url" ]);
    }

    public $breadcrumbs = [
        [
            "label" => "About",
            "url" => "/about",
        ],
    ];
    
    public function terms()
    {
        $this->breadcrumbs[] = [ "label" => "Terms of use"  ];
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/content/terms_of_use");
    }
    
    public function privacy()
    {
        $this->breadcrumbs[] = [ "label" => "Privacy policy"  ];
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/content/privacy_policy");
    }
    
    public function faq()
    {
        $this->breadcrumbs[] = [ "label" => "FAQ"  ];
        $this->twig->addGlobal("breadcrumbs", $this->breadcrumbs);
        $this->twig->display("twigs/content/faq");
    }
}
