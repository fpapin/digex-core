<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\ArrayCache;
use Digex\Console\Command;

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
            throw new \Exception('Silex\Provider\DoctrineServiceProvider is not registered');
        }

        $app['em'] = $app->share(function($app) {
            return EntityManager::create($app['db'], $app['em.config'], $app['db.event_manager']);
        });

        $app['em.annotation.driver'] = $app->share(function ($app) {

            if (isset($app['em.entities'])) {
                $paths = $app['em.entities'];
            } else {
                $paths = array();
            }

            $config = new Configuration();

            return $config->newDefaultAnnotationDriver($paths);
        });

        $app['em.config'] = $app->share(function($app) {
            $config = new Configuration();

            $config->setMetadataCacheImpl($app['em.cache']);
            $config->setQueryCacheImpl($app['em.cache']);

            if (isset($app['em.options']['proxy_dir'])) {
                $config->setProxyDir($app['em.options']['proxy_dir']);
                $config->setAutoGenerateProxyClasses(true);
            }

            if (isset($app['em.options']['proxy_namespace'])) {
                $config->setProxyNamespace($app['em.options']['proxy_namespace']);
            }

            $config->setMetadataDriverImpl($app['em.annotation.driver']);

            return $config;
        });

        //@todo user another cache type
        $app['em.cache'] = $app->share(function () {
            return new ArrayCache();
        });

        if (isset($app['em.class_path'])) {
            $app['autoloader']->registerNamespace('Doctrine\\ORM', $app['em.class_path']);
        }

        if (isset($app['console'])) {
            $app['console']->add(new Command\CreateSchemaDoctrineCommand());
            $app['console']->add(new Command\UpdateSchemaDoctrineCommand());
            $app['console']->add(new Command\DropSchemaDoctrineCommand());

            //@todo should be in a DBAL related provider
            $app['console']->add(new Command\DropDatabaseDoctrineCommand());
            $app['console']->add(new Command\CreateDatabaseDoctrineCommand());

            //if doctrine/fixtures is enabled
            if (class_exists('\Doctrine\Common\DataFixtures\AbstractFixture')) {
                $app['console']->add(new Command\LoadDataFixturesDoctrineCommand());
            }
        }
    }

	public function boot(Application $app) {}
}