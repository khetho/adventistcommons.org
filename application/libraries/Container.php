<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Container
{
	private $services;
	private $closures;
	private $built = false;
	
	public function get( $name )
	{
		if (!$this->built) {
			$this->build();
		}
		if (!isset($this->services[$name])) {
			if (!isset($this->closures[$name])) {
				throw new \Exception(sprintf('Service does not exists : %s', $name));
			}
			$this->services[$name] = $this->closures[$name]();
		}
		
		return $this->services[$name];
	}
	
	public function has( $name )
	{
		if (!$this->built) {
			$this->build();
		}
		
		return (isset($this->closures[ $name ]) || isset($this->services[ $name ]));
	}
	
	private function set( $name, $service ): void
	{
		if ($this->built) {
			throw new \Exception('Cannot add services to container since it was closed');
		}
		if ($service instanceof Closure) {
			$this->closures[$name] = $service;
			
			return;
		} elseif (is_object($service)) {
			$this->services[$name] = $service;
			
			return;
		}
		
		throw new \Exception('To define a service in the container, please provide an instance of the class
			or a closure that will create it when requested.');
	}
	
	private function build()
	{
		/****************************
		 * CODE IGNITER MODELS
		 ****************************/
		$ci =& get_instance();
		$this->set(
			\CI_DB_mysqli_driver::class,
			function () use($ci) {
				return $ci->load->database('', true);
			}
		);
		
		$this->set(
			\AdventistCommons\Import\Idml\IDMLextend::class,
			function () {
				return new \AdventistCommons\Import\Idml\IDMLextend(
					$this->get(\CI_DB_mysqli_driver::class)
				);
			}
		);

		$ci->load->library('twig');
		$this->set(
			\Twig_Environment::class,
			function () use ($ci) {
				return $ci->twig->getTwig();
			}
		);

		$this->set(
			\AdventistCommons\Export\Idml\Builder::class,
			function () {
				return new \AdventistCommons\Export\Idml\Builder(
					$this->get(\CI_DB_mysqli_driver::class),
					$this->get(\Twig_Environment::class)
				);
			}
		);
		
		$this->built = true;
	}
}
