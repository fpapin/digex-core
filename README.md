Digex
=====

A toolbox for the Silex Framework

* Yaml configurator
* Auto-enable some usefull Silex extensions from config files
    * UrlGeneratorExtension
    * TwigExtension
    * MonologExtension

Authors
-------

Damien Pitard <dpitard@digitas.fr>

Todo
----

* Cache the Yaml config
* Check server config with CLI
* Init sandbox with CLI
* Reimplement Restriction
* Add DoctrineOrmExtension (https://github.com/docteurklein/Silex/blob/73d044f7aa9a1ecb057091b32211995b9742ab93/src/Silex/Extension/DoctrineOrmExtension.php)
* Implement vendors install into digex OR build vendors into phar
* Manage environments without environment system (use index.php and index_dev.php)
* split log into environment logs