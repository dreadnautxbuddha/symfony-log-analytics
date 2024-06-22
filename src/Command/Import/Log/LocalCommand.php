<?php

namespace App\Command\Import\Log;

use App\Service\LogFileImporter\LogFileImporter;
use App\Util\File\SplFileObjectIterator;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function file_exists;
use function is_integer;

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
    public function __construct(
        protected ValidatorInterface $validator,
        protected LogFileImporter $logFileImporter,
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
        $this->addArgument(
            'offset',
            InputArgument::OPTIONAL,
            'Which line of the log file from which to start reading',
            0
        );
        $this->addArgument(
            'chunkSize',
            InputArgument::OPTIONAL,
            'The number of log entries that will be saved to the database in a single transaction',
            500
        );
        $this->addArgument(
            'limit',
            InputArgument::OPTIONAL,
            'Which line of the log file from which to stop reading'
        );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $offset = $input->getArgument('offset');
        $limit = $input->getArgument('limit');
        $chunkSize = $input->getArgument('chunkSize');

        if (! file_exists($path)
            || ! is_integer($offset)
            || $input->hasArgument('chunkSize') && ! is_integer($chunkSize)
            || $input->hasArgument('limit') && $limit !== null && ! is_integer($limit)) {
            return 1;
        }

        $this
            ->logFileImporter
            ->import(new SplFileObjectIterator(new SplFileObject($path)), $offset, $chunkSize, $limit);

        return 0;
    }
}
