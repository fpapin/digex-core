<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\DoctrineServiceProvider;
use Digex\YamlConfigLoader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Digex\Console\Command\SchemaCreateCommand;
use Digex\Console\Command\UpdateSchemaCommand;
        use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
/**
 * @see https://github.com/flintstones/DoctrineOrm
 * 
 * @author Damien Pitard <dpitard at digitas dot fr>
 * @author Vyacheslav Slinko
 * @copyright Digitas France
 */
class DoctrineORMServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if (!isset($app['db'])) {
            throw new \Exception('You must register DoctrineServiceProvider');
        }
        
        $app['em'] = $app->share(function () use ($app) {
            return EntityManager::create($app['db'], $app['em.config'], $app['db.event_manager']);
        });
        
        $app['em.config'] = $app->share(function () use ($app) {
            $config = new Configuration();

            $config->setMetadataCacheImpl($app['em.cache']);
            $config->setQueryCacheImpl($app['em.cache']);

            if (isset($app['db.proxy_dir'])) {
                $config->setProxyDir($app['db.proxy_dir']);
                $config->setAutoGenerateProxyClasses(true);
            }

            if (isset($app['db.proxy_namespace'])) {
                $config->setProxyNamespace($app['db.proxy_namespace']);
            }

            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());
            
            if (isset($app['db.entities'])) {
                $paths = $app['db.entities'];
            } else {
                $paths = array();
            }

            $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($paths));

            return $config;
        });

        //@todo user another cache type
        $app['em.cache'] = $app->share(function () {
            return new ArrayCache();
        });

        if (isset($app['db.orm.class_path'])) {
            $app['autoloader']->registerNamespace('Doctrine\\ORM', $app['db.orm.class_path']);
        }

        if (isset($app['console']) && class_exists('Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand')) {
            $app['console']->add(new SchemaCreateCommand());
            $app['console']->add(new UpdateSchemaCommand());
        }
    }
}