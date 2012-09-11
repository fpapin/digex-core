<?php

namespace Digex;

use Silex\WebTestCase as BaseWebTestCase;
use Symfony\Component\Finder\Finder;

abstract class WebTestCase extends BaseWebTestCase
{
    protected static $cache;

    /**
     * Finds the directory where the phpunit.xml(.dist) is stored.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * @return string The directory where phpunit.xml(.dist) is stored
     */
    protected static function getPhpUnitXmlDir()
    {
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the WebTestCase::createApplication() method.');
        }

        $dir = static::getPhpUnitCliConfigArgument();
        if ($dir === null &&
            (is_file(getcwd().DIRECTORY_SEPARATOR.'phpunit.xml') ||
            is_file(getcwd().DIRECTORY_SEPARATOR.'phpunit.xml.dist'))) {
            $dir = getcwd();
        }

        // Can't continue
        if ($dir === null) {
            throw new \RuntimeException('Unable to guess the app directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Finds the value of configuration flag from cli
     *
     * PHPUnit will use the last configuration argument on the command line, so this only returns
     * the last configuration argument
     *
     * @return string The value of the phpunit cli configuration option
     */
    private static function getPhpUnitCliConfigArgument()
    {
        $dir = null;
        $reversedArgs = array_reverse($_SERVER['argv']);
        foreach ($reversedArgs as $argIndex => $testArg) {
            if ($testArg === '-c' || $testArg === '--configuration') {
                $dir = realpath($reversedArgs[$argIndex - 1]);
                break;
            } elseif (strpos($testArg, '--configuration=') === 0) {
                $argPath = substr($testArg, strlen('--configuration='));
                $dir = realpath($argPath);
                break;
            }
        }

        return $dir;
    }

    /**
     * Attempts to guess the app location.
     *
     * When the app is located, the file is required and returned.
     *
     * @return Silex\Application The application
     */
    protected static function getApplication()
    {
        if (!isset(static::$cache['app'])) {
            $dir = isset($_SERVER['APP_DIR']) ? $_SERVER['APP_DIR'] : static::getPhpUnitXmlDir();

            $finder = new Finder();
            $finder->name('app.php')->depth(0)->in($dir);
            $results = iterator_to_array($finder);
            if (!count($results)) {
                throw new \RuntimeException('Either set APP_DIR in your phpunit.xml or override the WebTestCase::createApplication() method.');
            }

            $file = current($results);

            $env = 'test';
            static::$cache['app'] = require_once $file;
        }

        return static::$cache['app'];
    }

    /**
     * Create an application
     * 
     * @return Silex\Application The application
     */
    public function createApplication()
    {
        $app = static::getApplication();
        $app['debug'] = true;
        unset($app['exception_handler']);

        // $this->app['session.test'] = true;

        return $app;
    }
}