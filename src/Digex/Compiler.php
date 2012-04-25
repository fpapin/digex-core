<?php

namespace Digex;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\Process;

/**
 * The Compiler class compiles Digex
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class Compiler
{
    protected $version;

    /**
     * Compiles the Digex source code into one single Phar file.
     *
     * @param string $pharFile Name of the output Phar file
     */
    public function compile($pharFile = 'digex.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $process = new Process('git log --pretty="%h %ci" -n1 HEAD');
        if ($process->run() > 0) {
            throw new \RuntimeException('The git binary cannot be found.');
        }
        $this->version = trim($process->getOutput());

        $phar = new \Phar($pharFile, 0, 'digex.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->exclude('Tests')
            ->in(__DIR__.'/..')
            ->in(__DIR__.'/../../vendor/symfony/yaml/Symfony/Component/Yaml')
            ->in(__DIR__.'/../../vendor/symfony/console/Symfony/Component/Console')
            ->in(__DIR__.'/../../vendor/symfony/translation/Symfony/Component/Translation')
            ->in(__DIR__.'/../../vendor/symfony/twig-bridge/Symfony/Bridge/Twig')
            ->in(__DIR__.'/../../vendor/symfony/config/Symfony/Component/Config')
            ->in(__DIR__.'/../../vendor/doctrine/common/lib/Doctrine/Common')
            ->in(__DIR__.'/../../vendor/doctrine/dbal/lib/Doctrine/DBAL')
            ->in(__DIR__.'/../../vendor/doctrine/orm/lib/Doctrine/ORM')
            ->in(__DIR__.'/../../vendor/twig/twig/lib/Twig')
            ->in(__DIR__.'/../../vendor/monolog/monolog/src/Monolog')
        ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_classmap.php'));

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        //$phar->compressFiles(\Phar::GZ);

        unset($phar);
    }

    protected function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());
        $content = file_get_contents($file);
        if ($strip) {
            $content = self::stripWhitespace($content);
        }

        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    protected function getStub()
    {
        return <<<'EOF'
<?php

Phar::mapPhar('digex.phar');

require_once 'phar://digex.phar/vendor/autoload.php';

if ('cli' === php_sapi_name() && basename(__FILE__) === basename($_SERVER['argv'][0]) && isset($_SERVER['argv'][1])) {
    switch ($_SERVER['argv'][1]) {
        case 'version':
            printf("Digex version %s\n", Digex\Application::VERSION);
            break;

        default:
            printf("Unknown command '%s' (available commands: version, check, and update).\n", $_SERVER['argv'][1]);
    }

    exit(0);
}

__HALT_COMPILER();
EOF;
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * Based on Kernel::stripComments(), but keeps line numbers intact.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the whitespace removed
     */
    static public function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}