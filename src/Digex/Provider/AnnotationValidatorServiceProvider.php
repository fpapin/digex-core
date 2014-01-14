<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

/**
 * Replace the standard silex validator provider to support validation annotations
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 * @copyright Digitas France
 */
class AnnotationValidatorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['validator.mapping.class_metadata_factory'] = $app->share($app->extend('validator.mapping.class_metadata_factory', function ($factory, $app) {

            if (isset($app['annotation.reader'])) {
                return new ClassMetadataFactory(new AnnotationLoader($app['annotation.reader']));
            } else {
                return $factory;
            }
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {

    }
}