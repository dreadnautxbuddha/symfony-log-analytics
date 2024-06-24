<?php

namespace App\Service\LogFileImporter;

use App\Dto\Entity\LogEntry\Assembler\FromString;
use App\Util\File\Support\Contracts;
use Psr\Log\LoggerInterface;

/**
 * @package App\Service\LogFileImporter
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogFileImporter implements Support\Contracts\LogFileImporterInterface
{
    public function __construct(protected LogEntryDtoImporter $logEntryDtoImporter, protected LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function import(
        Contracts\ChunkableIterator & Contracts\PaginableIterator $iterator,
        int $offset,
        int $chunk_size,
        ?int $limit = null
    ): void
    {
        $iterator->seek($offset);
        $iterator->limit($limit);
        // Because we are chunking the lines that are being read from the file, each iteration using a loop will yield
        // an array of lines, instead of a string comprising a single line.
        $iterator->chunk($chunk_size);

        while ($iterator->valid()) {
            $lines = [];

            foreach ($iterator->current() as $line) {
                $assembler = new FromString($line);
                $log_entry_dto = $assembler->assemble();

                // Entity DTOs that cannot be assembled means that the supplied data to it is not enough to create one. Thus, we can skip it.
                if (empty($log_entry_dto)) {
                    $this->logger->warning('Skipping log entry with mismatched format', ['line' => $line]);

                    continue;
                }

                $lines[] = $log_entry_dto;
            }

            if (empty($lines)) {
                continue;
            }

            $this->logEntryDtoImporter->import($lines);
        }
    }
}
