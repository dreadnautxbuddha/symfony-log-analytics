<?php

namespace Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter;

use Dreadnaut\LogAnalyticsBundle\Dto\Entity\Support\Contracts\EntityDtoAssemblerInterface;
use Dreadnaut\LogAnalyticsBundle\Util\File\Support\Contracts;
use Psr\Log\LoggerInterface;
use SplFileObject;

/**
 * @package Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogFileImporter implements Support\Contracts\LogFileImporterInterface
{
    public function __construct(
        protected LogEntryDtoImporter $logEntryDtoImporter,
        protected LoggerInterface $logger,
        protected EntityDtoAssemblerInterface $assembler
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function import(
        Contracts\ChunkableIteratorInterface & Contracts\PaginableIteratorInterface $iterator,
        int $offset,
        int $chunk_size,
        ?int $limit = null
    ): void
    {
        $iterator->seek($offset);
        $iterator->limit($limit);
        $iterator->chunk($chunk_size, fn (SplFileObject $file) => [$file->key() + 1, $file->fgets()]);

        while ($iterator->valid()) {
            $importable_log_entry_dtos = [];

            // Because we are chunking the lines that are being read from the file, each iteration using a loop will
            // yield an array of lines, instead of a string comprising a single line.
            foreach ($iterator->current() as [$line_number, $line]) {
                $log_entry_dto = $this->assembler->assemble($line);

                // Entity DTOs that cannot be assembled means that the supplied data to it is not enough to create one.
                // Thus, we can skip it.
                if (empty($log_entry_dto)) {
                    // TODO: Update the test here to add an assertion on the line number that's being skipped.
                    $this
                        ->logger
                        ->warning('Skipping log entry with mismatched format', [
                            'line' => $line_number,
                            'content' => $line,
                        ]);

                    continue;
                }

                $importable_log_entry_dtos[] = $log_entry_dto;
            }

            if (empty($importable_log_entry_dtos)) {
                // Sadly, there are no importable log entry DTOs in this chunk -- most likely because they do not match
                // the pattern we are expecting.
                continue;
            }

            $this->logEntryDtoImporter->import($importable_log_entry_dtos);
        }
    }
}
