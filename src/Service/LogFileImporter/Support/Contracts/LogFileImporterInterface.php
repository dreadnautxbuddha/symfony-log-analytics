<?php

namespace App\Service\LogFileImporter\Support\Contracts;

use App\Util\File\SplFileObjectIteratorInterface;
use App\Util\File\Support\Contracts;

interface LogFileImporterInterface
{
    /**
     * Imports the log entries from the file iterator
     *
     * @param SplFileObjectIteratorInterface $iterator
     * @param int                            $offset
     * @param int                            $chunk_size
     * @param int|null                       $limit
     *
     * @return void
     */
    public function import(
        Contracts\ChunkableIteratorInterface & Contracts\PaginableIteratorInterface $iterator,
        int $offset,
        int $chunk_size,
        ?int $limit = null
    ): void;
}
