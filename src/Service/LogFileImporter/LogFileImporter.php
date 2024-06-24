<?php

namespace App\Service\LogFileImporter;

use App\Dto\Entity\LogEntry\Assembler\FromString;
use App\Util\File\Support\Contracts;

use function array_filter;
use function array_map;

/**
 * @package App\Service\LogFileImporter
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogFileImporter implements Support\Contracts\LogFileImporterInterface
{
    public function __construct(protected LogEntryDtoImporter $logEntryDtoImporter)
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
            $lines = array_filter(
                array_map(fn (string $line) => (new FromString($line))->assemble(), $iterator->current())
            );

            if (empty($lines)) {
                continue;
            }

            $this->logEntryDtoImporter->import($lines);
        }
    }
}
