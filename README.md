Digex
=====

A toolbox for the Silex Framework

* Yaml config
* Auto-enable some usefull Silex extensions from config files
    * UrlGeneratorExtension
    * TwigExtension
    * MonologExtension

Authors
-------

Damien Pitard <dpitard@digitas.fr>

Changelog
---------

* 1.2.0
    * replaced ini file by yaml
    * removed custom exception handler
    * moved and renamed Digitas libraries to Digex

* 1.1.0
    * added ApplicationExtension
    * added RestrictionExtension

Todo
----
* use SimplePhar to compile the lib into a phar file (https://github.com/CHH/SimplePhar)
* Cache the Yaml config
* Check server config with CLI
* Init sandbox with CLI
* Reimplement Restriction
* add DoctrineOrmExtension (https://github.com/docteurklein/Silex/blob/73d044f7aa9a1ecb057091b32211995b9742ab93/src/Silex/Extension/DoctrineOrmExtension.php)
* implement vendors install into digex