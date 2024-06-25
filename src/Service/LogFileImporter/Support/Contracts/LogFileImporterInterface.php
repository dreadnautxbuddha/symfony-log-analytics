<?php

namespace Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter\Support\Contracts;

use Dreadnaut\LogAnalyticsBundle\Util\File\SplFileObjectIteratorInterface;
use Dreadnaut\LogAnalyticsBundle\Util\File\Support\Contracts;

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
