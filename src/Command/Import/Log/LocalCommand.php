<?php

namespace App\Command\Import\Log;

use App\Service\LogFileImporter\Support\Contracts\LogFileImporterInterface;
use App\Util\File\SplFileObjectIterator;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function file_exists;
use function filter_var;
use const FILTER_VALIDATE_INT;

/**
 * A command responsible for importing a log file that exists in the filesystem.
 *
 * @package App\Command\Import\Log
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
#[AsCommand(
    name: 'import:log:local',
    description: 'Import a log file that exists in the filesystem',
)]
class LocalCommand extends Command
{
    public function __construct(protected LogFileImporterInterface $logFileImporter, ?string $name = null)
    {
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
     * @todo Map the validation rules here into a DTO for reusability?
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $offset = $input->getOption('offset');
        $limit = $input->getOption('limit');
        $chunk_size = $input->getOption('chunk-size');

        if (! file_exists($path)) {
            return $this->fail($output, "The file at the specified path `{$path}` could not be found.");
        }

        if ($input->hasOption('offset') && filter_var($offset, FILTER_VALIDATE_INT) === false) {
            return $this->fail($output, "The offset `{$offset}` must be an integer.");
        }

        if ($input->hasOption('chunk-size') && filter_var($chunk_size, FILTER_VALIDATE_INT) === false) {
            return $this->fail($output, "The chunk size `{$chunk_size}` must be an integer.");
        }

        if ($input->hasOption('limit') && $limit !== null && filter_var($limit, FILTER_VALIDATE_INT) === false) {
            return $this->fail($output, "The limit `{$limit}` must be an integer.");
        }

        $this
            ->logFileImporter
            ->import(new SplFileObjectIterator(new SplFileObject($path)), $offset, $chunk_size, $limit);

        return Command::SUCCESS;
    }

    /**
     * Writes an error message to the output and returns a failure exit code.
     *
     * @param OutputInterface $output
     * @param string          $message
     *
     * @return int
     */
    protected function fail(OutputInterface $output, string $message): int
    {
        $output->writeln("<error>{$message}</error>");

        return self::FAILURE;
    }
}
