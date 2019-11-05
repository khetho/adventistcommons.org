<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Hybridauth\Hybridauth;

class HybridAuthLib extends Hybridauth
{
	function __construct($config = array())
	{
		$ci =& get_instance();
		$ci->load->helper('url_helper');

		$config['base_url'] = site_url((config_item('index_page') == '' ? SELF : '') . $config['base_url']);

		parent::__construct($config);

		log_message('debug', 'HybridAuthLib Class Initalized');
	}


	public function providerEnabled($provider)
	{
		return isset($this->config['providers'][$provider]) && $this->config['providers'][$provider]['enabled'];
	}
}

/* End of file HybridAuthLib.php */
/* Location: ./application/libraries/HybridAuthLib.php */
