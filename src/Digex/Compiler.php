<?php

namespace Digex;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright Digitas France
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
            ->in(__DIR__.'/..')
//            ->in(__DIR__.'/../../vendor/Symfony/Component/ClassLoader')
//            ->in(__DIR__.'/../../vendor/Symfony/Component/Process')
            //->in(__DIR__.'/../../vendor/Symfony/Component/Finder')
            ->in(__DIR__.'/../../vendor/Symfony/Component/Console')
            ->in(__DIR__.'/../../vendor/Symfony/Component/Yaml')
        ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../config/deps.yml'), false);
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../autoload.php'));

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        // $phar->compressFiles(\Phar::GZ);

        unset($phar);
    }

    protected function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());
        $content = file_get_contents($file);
        if ($strip) {
            $content = self::stripComments($content);
        }

        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    protected function getStub()
    {
        return <<<'EOF'
<?php

use Digex\Digex;

Phar::mapPhar('digex.phar');

require_once 'phar://digex.phar/autoload.php';

if ('cli' === php_sapi_name() && basename(__FILE__) === basename($_SERVER['argv'][0]) && isset($_SERVER['argv'][1])) {
    switch ($_SERVER['argv'][1]) {
        case 'version':
            printf("Digex version %s\n", Application::VERSION);
            break;

        default:
            printf("Unknown command '%s' (available commands: version).\n", $_SERVER['argv'][1]);
    }

    exit(0);
}

__HALT_COMPILER();
EOF;
    }

    /**
     * Removes comments from a PHP source string.
     *
     * Based on Kernel::stripComments(), but keeps line numbers intact.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the comments removed
     */
    static public function stripComments($source)
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
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}
