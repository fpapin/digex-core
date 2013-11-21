<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Digex\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Finder\Finder;

/**
 * Command that places bundle web assets into a given directory.
 *
 * @author Frédéric PAPIN <frederic.papin@digitas.com>
 */
class AssetsInstallCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('assets:install')
            ->setDefinition(array(
                new InputArgument('origin', InputArgument::OPTIONAL, 'The target to deploy', 'src'),
                new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web')
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs bundle assets into a given
directory (e.g. the web directory).

<info>php %command.full_name% web</info>

The "Resources/public" directory of each bundle will be copied into it.
EOT
            )
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');
        $originArg = $input->getArgument('origin') . '/';

        $finder = new Finder();
        $finder->directories()->name('public')->in($originArg);
        $filesystem = new Filesystem();

        foreach ($finder as $file) {
            $originDir =  $originArg . $file->getRelativePathname();
            $explodeFilePath = explode('/', trim($file->getRelativePathname(), '/'));
            $bundleName = $explodeFilePath[count($explodeFilePath) - 3];
            $bundlesDir = $targetArg . '/';
            $targetDir  = $bundlesDir . $bundleName;
            $filesystem->mkdir($targetDir, 0777);
            $filesystem->mirror($originDir, $targetDir, Finder::create()->in($originDir));
        }
    }
}
