<?php

namespace Dreadnaut\LogAnalyticsBundle\Command\Import\Log;

use Dreadnaut\LogAnalyticsBundle\Command\Import\Log\Input\Cli;
use Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter\Support\Contracts\LogFileImporterInterface;
use Dreadnaut\LogAnalyticsBundle\Util\File\SplFileObjectIterator;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * A command responsible for importing a log file that exists in the filesystem.
 *
 * @package Dreadnaut\LogAnalyticsBundle\Command\Import\Log
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
#[AsCommand(
    name: 'import:log:local',
    description: 'Import a log file that exists in the filesystem',
)]
class LocalCommand extends Command
{
    public function __construct(
        protected LogFileImporterInterface $logFileImporter,
        protected ValidatorInterface $validator,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the log file');
        $this->addOption(
            'offset',
            null,
            InputOption::VALUE_REQUIRED,
            'Which line of the log file from which to start reading',
            0
        );
        $this->addOption(
            'chunk-size',
            null,
            InputOption::VALUE_REQUIRED,
            'The number of log entries that will be saved to the database in a single transaction',
            500
        );
        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_REQUIRED,
            'Which line of the log file from which to stop reading'
        );
    }

    /**
     * {@inheritDoc}
     *
     * @todo Might be a good idea to have a mapping attribute class for argument and option validation.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $offset = $input->getOption('offset');
        $limit = $input->getOption('limit');
        $chunkSize = $input->getOption('chunk-size');
        $errors = $this->validator->validate(new Cli($path, $offset, $limit, $chunkSize));

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln("<error>{$error->getMessage()}</error>");
            }

            return Command::FAILURE;
        }

        $this
            ->logFileImporter
            ->import(new SplFileObjectIterator(new SplFileObject($path)), $offset, $chunkSize, $limit);

        return Command::SUCCESS;
    }
}
