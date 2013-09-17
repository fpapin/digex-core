<?php

namespace Digex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;

/**
 * Replace the standard silex validator provider to support validation annotations
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 * @copyright Digitas France
 */
class AnnotationValidatorServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['validator.mapping.class_metadata_factory'] = $app->share($app->extend('validator.mapping.class_metadata_factory', function () use ($app) {

            if (isset($app['annotation.reader'])) {
                return new ClassMetadataFactory(new AnnotationLoader($app['annotation.reader']));
            } else {
                return new ClassMetadataFactory(new StaticMethodLoader());
            }
        }));
    }

    public function boot(Application $app) {}
}