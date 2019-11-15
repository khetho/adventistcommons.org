<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Container
{
    private $services;
    private $closures;
    private $built = false;
    
    public function get($name)
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
    
    public function has($name)
    {
        if (!$this->built) {
            $this->build();
        }
        
        return (isset($this->closures[ $name ]) || isset($this->services[ $name ]));
    }
    
    private function set($name, $service): void
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
            function () use ($ci) {
                return $ci->load->database('', true);
            }
        );
        $this->set(
            \Product_model::class,
            function () use ($ci) {
                $ci->load->model('product_model');
                return $ci->product_model;
            }
        );
        $this->set(
            \Project_model::class,
            function () use ($ci) {
                $ci->load->model('project_model');
                return $ci->project_model;
            }
        );
        
        $this->set(
            \AdventistCommons\Idml\Importer::class,
            function () {
                return new \AdventistCommons\Idml\Importer(
                    $this->get(\CI_DB_mysqli_driver::class)
                );
            }
        );
        
        $this->set(
            \AdventistCommons\Idml\Translator::class,
            function () {
                return new \AdventistCommons\Idml\Translator(
                    $this->get(\Product_model::class),
                    $this->get(\Project_model::class)
                );
            }
        );
        
        $this->set(
            \AdventistCommons\Idml\HolderBuilder::class,
            function () {
                return new \AdventistCommons\Idml\HolderBuilder(
                    $this->get(\AdventistCommons\Idml\DomManipulation\StoryBasedOnTags::class),
                    realpath(__DIR__.'/../../uploads')
                );
            }
        );
    
        $this->set(
            \AdventistCommons\Idml\ContentBuilder::class,
            function () {
                return new \AdventistCommons\Idml\ContentBuilder();
            }
        );
    
        $this->set(
            \AdventistCommons\Idml\DomManipulation\StoryBasedOnTags::class,
            function () {
                return new \AdventistCommons\Idml\DomManipulation\StoryBasedOnTags(
                    $this->get(\AdventistCommons\Idml\ContentBuilder::class)
                );
            }
        );

        $this->built = true;
    }
}
