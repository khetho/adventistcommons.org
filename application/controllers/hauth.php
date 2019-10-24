<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HAuth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library( [ "ion_auth", "twig" ] );

		$user = $this->ion_auth->user()->row();
		if ( $user ) {
			$this->user = $user;
			$this->twig->addGlobal( "user",  $user );
		}
	}

	public function index()
	{

		$this->load->view('hauth/home');
	}

	public function login($provider)
	{
		// log_message('debug', "controllers.HAuth.login($provider) called");
		try
		{
			// log_message('debug', 'controllers.HAuth.login: loading HybridAuthLib');
			$this->load->library('HybridAuthLib');

			if ($this->hybridauthlib->providerEnabled($provider))
			{
				// log_message('debug', "controllers.HAuth.login: service $provider enabled, trying to authenticate.");
				
				$service = $this->hybridauthlib->authenticate($provider);

				if ($service->isUserConnected())
				{
					// log_message('debug', 'controller.HAuth.login: user authenticated.');

					$user_profile = $service->getUserProfile();
					// log_message('info', 'controllers.HAuth.login: user profile:'.PHP_EOL.print_r($user_profile, TRUE));

					$this->social_login($user_profile);
				}
				else // Cannot authenticate user
				{
					show_error('Cannot authenticate user');
				}
			}
			else // This service is not enabled.
			{
				// log_message('error', 'controllers.HAuth.login: This provider is not enabled ('.$provider.')');
				show_404($_SERVER['REQUEST_URI']);
			}
		}
		catch(Exception $e)
		{
			$error = 'Unexpected error';
			switch($e->getCode())
			{
				case 0 : $error = 'Unspecified error.'; break;
				case 1 : $error = 'Hybriauth configuration error.'; break;
				case 2 : $error = 'Provider not properly configured.'; break;
				case 3 : $error = 'Unknown or disabled provider.'; break;
				case 4 : $error = 'Missing provider application credentials.'; break;
				case 5 : log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection.');
				         //redirect();
				         if (isset($service))
				         {
				         	log_message('debug', 'controllers.HAuth.login: logging out from service.');
				         	$service->logout();
				         }
				         show_error('User has cancelled the authentication or the provider refused the connection.');
				         break;
				case 6 : $error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
				         break;
				case 7 : $error = 'User not connected to the provider.';
				         break;
			}

			if (isset($service))
			{
				$service->logout();
			}

			log_message('error', 'controllers.HAuth.login: '.$error);
			show_error('Error authenticating user.');
		}
	}

	public function endpoint()
	{

		// log_message('debug', 'controllers.HAuth.endpoint called.');
		// log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: '.print_r($_REQUEST, TRUE));

		if ($_SERVER['REQUEST_METHOD'] === 'GET')
		{
			// log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
			$_GET = $_REQUEST;
		}

		// log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
		require_once APPPATH.'/third_party/hybridauth/index.php';

	}

	/**
	 * save profile data and create session
	 * @param  [type] $user_profile [description]
	 * @return [type]               [description]
	 */
	private function social_login($user_profile)
	{
	    $this->db->where('email', $user_profile->email);
	    $this->db->limit(1);
	    $users = $this->db->count_all_results('users');

	    if(!isset($users) || $users<1)
	    {
	        $password = $this->generatePasswordString();

	        $identity = $user_profile->email;

	        $additional_data = [
	            'first_name' => $user_profile->firstName ?? '',
	            'last_name' => $user_profile->lastName ?? '',
	            'product_notify' => true
	        ];
	        $register_id = $this->ion_auth->register($identity, $password, $user_profile->email, $additional_data);
	        if($register_id)
	        {
	            $this->ion_auth->activate($register_id);
	            $this->ion_auth->login($user_profile->email, $password, TRUE);

	            redirect( "/user/register_profile", "refresh" );
	        }
	    }
	    else
	    {
	        $user = $this->db->where(array('email'=>$user_profile->email))->limit(1)->get('users')->row();          
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

/* End of file hauth.php */
/* Location: ./application/controllers/hauth.php */
