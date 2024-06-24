<?php

namespace App\Util\File\Support\Contracts;

use Closure;
use RecursiveIterator;
use SeekableIterator;
use SplFileObject;

interface PaginableIterator extends RecursiveIterator, SeekableIterator
{
    /**
     * Limits the number of lines to be yielded.
     *
     * @param int|null $limit
     *
     * @return void
     */
    public function limit(?int $limit): void;
}
