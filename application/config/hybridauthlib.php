<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------
$config =
	array(
		// set on "base_url" the relative url that point to HybridAuth Endpoint
		'base_url' => '/hauth/endpoint',
		'callback' => 'https://adventistcommons.local.org/hauth/login/',


	    // Providers specifics.
	    'providers' => [
	        "Google" => array (
				"enabled" => true,
				"keys"    => array ( 
					"id" => "589167199141-ngsf1inuve1s8ajh3p30qgdl5l4j3i0i.apps.googleusercontent.com", 
					"secret" => "kSmIoxs77BK2QAoeqoIh5XRH" ),
			),

			"Facebook" => array (
				"enabled" => true,
				"keys"    => array ( 
					"id" => "467450287190113", 
					"secret" => "e5924a435d22f68b1c1fa2dff9411b93" 
				),
				'scope'   => 'email'
			),

			"Twitter" => array (
				"enabled" => true,
				"keys"    => array ( 
					"key" => "QmKOIEpdeDyDZyRxTpE7x69pW", 
					"secret" => "NZadmpZ6WYynuWnx4joVfGIXGQesmsRU0tsRzgHPxDahax0QdS",
					"includeEmail" => true
				)
			)
	    ],

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => (ENVIRONMENT == 'development'),

		"debug_file" => APPPATH.'/../var/log/hybridauth.log',
	);


/* End of file hybridauthlib.php */
/* Location: ./application/config/hybridauthlib.php */
