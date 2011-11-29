<?php

namespace Digex\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Digex\Application;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class VersionCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('digex:version')
            ->setDescription('Get the Digex version')
            ->setHelp(<<<EOF
The <info>digex:version</info> command returns the Digex version

<info>php app/console digex:version</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("Digex version %s", Application::VERSION));
    }
}