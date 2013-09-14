<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * Replace the doctrine ORM annotation driver to allow several annotation services to work at same time
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 * @copyright Digitas France
 */
class AnnotationReaderServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['annotation.reader'] = $app->share(function () use ($app) {

            $reader = new AnnotationReader();

            return new CachedReader($reader, new ArrayCache(), $app['debug']);
        });

        if (isset( $app['em.annotation.driver'])) {
            $app['em.annotation.driver'] = $app->share($app->extend('em.annotation.driver', function () use ($app) {
                if (isset($app['em.options']['entities'])) {
                    $paths = $app['em.options']['entities'];
                } else {
                    $paths = array();
                }

                return new AnnotationDriver($app['annotation.reader'], (array) $paths);
            }));
        }
    }

    public function boot(Application $app) {}
}