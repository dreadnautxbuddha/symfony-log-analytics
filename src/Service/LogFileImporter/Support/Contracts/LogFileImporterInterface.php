<?php

namespace App\Service\LogFileImporter\Support\Contracts;

use App\Util\File\SplFileObjectIterator;
use App\Util\File\Support\Contracts;

interface LogFileImporterInterface
{
    /**
     * Imports the log entries from the file iterator
     *
     * @param SplFileObjectIterator $iterator
     * @param int                   $offset
     * @param int                   $chunk_size
     * @param int|null              $limit
     *
     * @return void
     */
    public function import(
        Contracts\ChunkableIterator & Contracts\PaginableIterator $iterator,
        int $offset,
        int $chunk_size,
        ?int $limit = null
    ): void;
}
