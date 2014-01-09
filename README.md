Digex-Core
==========

[![Build Status](https://secure.travis-ci.org/digitas/digex-core.png)](http://travis-ci.org/digitas/digex-core)
[![Total Downloads](https://poser.pugx.org/digitas/digex-core/downloads.png)](https://packagist.org/packages/digitas/digex-core)

Core library for [Digex](https://github.com/digitas/digex)

If you have identified a bug in *Digex*, please use the [the Digex bug tracker](https://github.com/digitas/digex/issues).

Getting Started
---------------

You can get a fully working [sandbox here](https://github.com/digitas/digex)

Or you can install it manually in an existent Silex project.

Register the following provider:

    $app->register(new Digex\Provider\DigexServiceProvider(), array(
        'digex.app_dir' => __DIR__ //your app directory
    ));

You can override some parameters:

    $app->register(new Digex\Provider\DigexServiceProvider(), array(
        'digex.config_dir' => __DIR__ . '/config',
        'digex.logs_dir' => __DIR__ . '/logs',
        'digex.template_dir' => __DIR__ . '/views',
        'digex.cache_dir' => __DIR__ . '/cache',

        //doctrine DBAL
        'db.options' => array(
            'driver'    => 'pdo_mysql',
            'dbname'    => 'digex',
            'path'      => null,
            'host'      => 'localhost',
            'user'      => 'root',
            'password'  => 'a-password'
        ),

        //doctrine ORM
        'em.options' => array(...),
        'em.entities'    => array('src/Digitas/Demo/Entity'),
        'em.fixtures'    => array(''src/Digitas/Demo/DataFixtures/ORM')

        //translation
        'translation.allowed' => array('en')

        //annotation
        'digex.loader_filename' => __DIR__ . '/../vendor/autoload.php'
    ));

Digex automatically enables services depending on composer requirements.

To enable console:

    composer require "symfony/console": ">=2.1,<=2.2"

To enable configuration:

    composer require "symfony/yaml":"2.1.*" "igorw/config-service-provider":"1.2.*"

To enable logging (Monolog):

    composer require "monolog/monolog":"1.0.*"

To enable database query abstraction (Doctrine DBAL):

    composer require "doctrine/dbal":"2.2.*"

To enable database ORM (Doctrine ORM):

    composer require "doctrine/orm":"2.2.*"

> You must provide a list of directories in parameter `em.entities`.

To enable fixtures load command (Doctrine DataFixtures):

    composer require "doctrine/data-fixtures":"1.0.*""

> You must provide a list of directories in parameter `em.fixtures`.

To enable validation annotation:

    composer require "symfony/validator":"2.1.*

To enable translation:

    composer "symfony/translation":"2.1.*"

To enable twig template:

   composer "twig/twig":"1.9.*

To enable form:

    composer require "symfony/form":"2.1.*"

TO enable security:

    composer require "symfony/security":"2.1.*"

Compatibility
-------------

### Enable the old configuration service (Digex <= 1.1)

Install the required library:

    composer require "symfony/yaml":"2.1.*"

And register the service:

    $app->register(new \Digex\Provider\ConfigurationServiceProvider(), array(
        'config.config_dir'    => __DIR__ . '/config',
        'config.env'    => isset($env)?$env:null,
    ));

Copyright
---------

Copyright (c) 2011-2012, Digitas France

All rights reserved.

Licence
-------

Digex-Core is released under the 3-clause BSD licence.

Please read the LICENCE file.

Contributors
------------

[See the Honour Roll](https://github.com/digitas/digex-core/graphs/contributors)


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/digitas/digex-core/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

