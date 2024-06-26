<?php

namespace Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter\Support\Contracts;

use Dreadnaut\LogAnalyticsBundle\Util\File\SplFileObjectIterator;
use Dreadnaut\LogAnalyticsBundle\Util\File\Support\Contracts;

interface LogFileImporterInterface
{
    /**
     * Imports the log entries from the file iterator
     *
     * @param SplFileObjectIterator $iterator
     * @param int                   $offset
     * @param int                   $chunkSize
     * @param int|null              $limit
     *
     * @return void
     */
    public function import(
        Contracts\ChunkableIteratorInterface & Contracts\PaginableIteratorInterface $iterator,
        int $offset,
        int $chunkSize,
        ?int $limit = null
    ): void;
}
