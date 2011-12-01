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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;

/**
 * Command to execute the SQL needed to generate the database schema for
 * a given entity manager.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class SchemaCreateCommand extends CreateCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('digex:schema:create')
            ->setDescription('Executes (or dumps) the SQL needed to generate the database schema.')
            ->setHelp(<<<EOT
The <info>digex:schema:create</info> command executes the SQL needed to
generate the database schema for the default entity manager:

<info>./app/console digex:schema:create</info>

Finally, instead of executing the SQL, you can output the SQL:

<info>./app/console digex:schema:create --dump-sql</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication());

        parent::execute($input, $output);
    }
}
