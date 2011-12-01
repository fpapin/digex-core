<?php

namespace Digex;

use Silex\Application as SilexApplication;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
abstract class Application extends SilexApplication
{
    const VERSION = '@package_version@';
    
    abstract public function configure();

    abstract public function getControllers();
    
    abstract public function getServices();

    public function __construct()
    {
        parent::__construct();
        
        $app = $this;
        
        $this->configure();
        
        $this->registerControllerProviders();
        $this->registerServiceProviders();
    }
    
    protected function registerControllerProviders()
    {
        foreach($this->getControllers() as $route => $controller) {
            $this->mount($route, $controller);
        }
    }
    
    protected function registerServiceProviders()
    {
        foreach($this->getServices() as $controller) {
            $this->register($controller);
        }
    }
}
