<?php

namespace Digex\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
//use Symfony\Component\Process\Process;
use Digex\Provider\LazyRegisterServiceProvider;


/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class VendorCommand extends AppAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('digex:vendor')
            ->setDescription('Install the required vendors')
            ->setHelp(<<<EOF
The <info>vendor</info> command downloads the required 3rd party librairies.
    
The git binary is required.

<info>php app/console digex:vendor</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApp();
        if (!isset($app['vendor_dir'])) {
            throw new \Exception('Undefined $app["vendor_dir"] parameter');
        }
        
        $vendorDir = $app['vendor_dir'];

        if (!is_dir($vendorDir)) {
            mkdir($vendorDir, 0777, true);
        }
        
        $deps = Yaml::parse(__DIR__.'/../../../../config/deps.yml');
        
        if (!isset($deps['deps'])) {
            throw new \Exception('Invalid configuration format');
        }
        
        foreach($deps['deps'] as $name => $libs) {

            if (LazyRegisterServiceProvider::isEnabled($app, $name)) {
                
                foreach($libs as $target => $dep) {

                    // revision
                    if (isset($dep['version'])) {
                        $rev = $dep['version'];
                    } else {
                        $rev = 'origin/HEAD';
                    }

                    // install dir
                    $installDir = $vendorDir.'/'.$target;

                    //@todo is enabled ?

                    $output->writeln(sprintf("Installing <info>%s</info>", $name));

                    if (!isset($dep['url'])) {
                        throw new \Exception(sprintf('The "url" value for the "%s" dependency must be set.', $name));
                    }
                    $url = $dep['url'];

                    if (isset($dep['scm'])) {
                        $scm = $dep['scm'];
                    } else {
                        $scm = 'git';
                    }

                    switch ($scm) {
                        case 'git':
                            $this->doGitInstall($url, $rev, $installDir);
                            break;
                        default:
                            throw new \Exception(sprintf('Unsupported scm type %scm', $scm));
                    }

                }
            }
        }
    }
    
    protected function doGitInstall($url, $rev, $installDir)
    {
        if (!is_dir($installDir)) {
            system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));

//            $process = new Process('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir));
//            if ($process->run() > 0) {
//                throw new \RuntimeException('The git binary cannot be found.');
//            }
        }
        
        system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
        
//        $process = new Process(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
//        if ($process->run() > 0) {
//            throw new \RuntimeException('The git binary cannot be found.');
//        }

//        system(sprintf('cd %s && git submodule update --init', escapeshellarg($installDir)));
    }
}