<?php

namespace Digex\Console\Command;

use Digex\Digex;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Damien Pitard <dpitard at digitas.fr>
 * @copyright Digitas France
 */
class DigexVersionCommand extends Command
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
The <info>digex:version</info> command install the sandbox
    - 3rd party libraries download

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
        $output->writeln(Digex::getVersion());
    }
}