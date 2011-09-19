Digex
=====

A toolbox for Silex Framework

* Yaml config
* auto-enable some usefull Silex extensions

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
* use SimplePhar to compile the lib into a phar file
* Cache the Yaml config
* Check server config with CLI
* Init sandbox with CLI
* Reimplement Restriction
* add DoctrineOrmExtension (https://github.com/docteurklein/Silex/blob/73d044f7aa9a1ecb057091b32211995b9742ab93/src/Silex/Extension/DoctrineOrmExtension.php)
* implement vendors install into digex