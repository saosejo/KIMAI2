<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Constants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command used to fetch Kimai version information.
 */
class VersionCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('kimai:version')
            ->setDescription('Receive version information')
            ->setHelp('This command allows you to fetch various version information about Kimai.')
            ->addOption('name', null, InputOption::VALUE_NONE, 'Display the major release name')
            ->addOption('candidate', null, InputOption::VALUE_NONE, 'Display the current version candidate (e.g. "stable" or "dev")')
            ->addOption('short', null, InputOption::VALUE_NONE, 'Display the version only')
            ->addOption('semver', null, InputOption::VALUE_NONE, 'Semantical versioning (SEMVER) compatible version string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('semver')) {
            $io->writeln(Constants::VERSION . '-' . Constants::STATUS);

            return 0;
        }

        if ($input->getOption('short')) {
            $io->writeln(Constants::VERSION);

            return 0;
        }

        if ($input->getOption('name')) {
            $io->writeln(Constants::NAME);

            return 0;
        }

        if ($input->getOption('candidate')) {
            $io->writeln(Constants::STATUS);

            return 0;
        }

        $io->writeln(Constants::SOFTWARE . ' - ' . Constants::VERSION . ' ' . Constants::STATUS . ' (' . Constants::NAME . ') by Kevin Papst and contributors.');

        return 0;
    }
}
