<?php

namespace Digex;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Digex\Provider\ConfigurationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;

/**
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @copyright Digitas France
 */
abstract class StandardApplication implements ControllerProviderInterface, ServiceProviderInterface
{
    protected $appDir;
    protected $vendorDir;
    protected $configDir;
    
    public function __construct($appDir, $vendorDir, $configDir = null)
    {
        $this->appDir = $appDir;
        $this->vendorDir = $vendorDir;
        if (null === $configDir) {
            $this->configDir = $this->appDir . '/config';
        } else {
            $this->configDir = $configDir;
        }
    }
    
    public function register(Application $app)
    {
        //register Configuration extension
        $app->register(new ConfigurationServiceProvider($this->configDir));
        
        //register UrlGenerator extension
        if (isset( $app['app.providers']['url_generator']) &&  $app['app.providers']['url_generator']) {
            $app->register(new UrlGeneratorServiceProvider());
        }

        //register Twig
        if (isset( $app['app.providers']['twig']) &&  $app['app.providers']['twig']) {

            if (!isset($app['twig.path'])) {
                $app['twig.path'] = $this->appDir . '/Resources/views';
            }
            
            if (!isset($app['twig.class_path'])) {
                $app['twig.class_path'] = $this->vendorDir . '/twig/lib';
            }
            
            if (!isset($app['twig.options']) && (!isset($app['debug']) || !$app['debug'])) {
                $app['twig.options'] = array('cache' => $this->appDir . '/cache/twig');
            }
            
            $app->register(new TwigServiceProvider());
        }

        //register Monolog
        if (isset( $app['app.providers']['monolog']) &&  $app['app.providers']['monolog']) {
            $app->register(new MonologServiceProvider(), array(
                'monolog.logfile'       => $this->appDir.'/logs/app.log',
                'monolog.class_path'    => $this->vendorDir.'/monolog/src',
                'monolog.name' => 'app'
            ));
        }
    }
}
