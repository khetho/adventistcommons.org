<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Facebook_Authentication extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library( [ "ion_auth", "twig" ] );

        $user = $this->ion_auth->user()->row();
        if ( $user ) {
            $this->user = $user;
            $this->twig->addGlobal( "user",  $user );
        }
        
        // Load facebook library
        $this->load->library('facebook');
        
    }
    
    public function index() {
        // Check if user is logged in
        if ($this->facebook->is_authenticated())
        {
            // Get user facebook profile details
            $fbUser = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,gender,picture');
            
            // format data
            $user_profile = $this->get_user_profile($fbUser);

            $this->social_login($user_profile);

        } else
        {
            show_error('Error authenticating user.');
        }
        
        // Load login & profile view
        $this->load->view('user_authentication/index',$data);
    }

    public function logout() {
        // Remove local Facebook session
        $this->facebook->destroy_session();
        // Redirect to login page
        redirect('login');
    }

    /**
     * format user profile data from facebook
     * @param  [type] $fbUser [description]
     * @return [type]         [description]
     */
    private function get_user_profile($fbUser)
    {
        $user_profile = array();
        $user_profile['oauth_provider'] = 'facebook';
        $user_profile['identifier']     = !empty($fbUser['id'])?$fbUser['id']:'';;
        $user_profile['first_name']     = !empty($fbUser['first_name'])?$fbUser['first_name']:'';
        $user_profile['last_name']      = !empty($fbUser['last_name'])?$fbUser['last_name']:'';
        $user_profile['email']          = !empty($fbUser['email'])?$fbUser['email']:'';
        $user_profile['photoURL']       = !empty($fbUser['picture']['data']['url'])?$fbUser['picture']['data']['url']:'';
        $user_profile['profileURL']     = !empty($fbUser['link'])?$fbUser['link']:'';

        return $user_profile;
    }

    /**
     * save profile data and create session
     * @param  [type] $user_profile [description]
     * @return [type]               [description]
     */
    private function social_login($user_profile)
    {
        $this->db->where('email', $user_profile['email']);
        $this->db->limit(1);
        $users = $this->db->count_all_results('users');

        if(!isset($users) || $users<1)
        {
            $password = $this->generatePasswordString();

            $identity = $user_profile['email'];

            $additional_data = [
                'first_name' => $user_profile['first_name'],
                'last_name' => $user_profile['last_name'],
                'product_notify' => true
            ];
            $register_id = $this->ion_auth->register($identity, $password, $user_profile['email'], $additional_data);

            if ($register_id)
            {
                $this->ion_auth->activate($register_id);
                $this->ion_auth->login($user_profile['email'], $password, TRUE);

                redirect( "/user/register_profile", "refresh" );
            }
        }
        else
        {
            $user = $this->db->where(array('email'=>$user_profile['email']))->limit(1)->get('users')->row();          
            $_SESSION['identity'] = $user->username;
            $_SESSION['username'] = $user->username;
            $_SESSION['email'] = $user->email;
            $_SESSION['user_id'] = $user->id; //everyone likes to overwrite id so we'll use user_id
            $_SESSION['old_last_login'] = $user->last_login;
            redirect('/projects', 'refresh');
        } 
    }

    /**
     * a little helper for generating passwords
     * @return [type] [description]
     */
    private function generatePasswordString()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr( str_shuffle($chars), 0, 10 );
    }
}
