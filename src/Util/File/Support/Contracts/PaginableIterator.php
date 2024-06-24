<?php

namespace App\Util\File\Support\Contracts;

use RecursiveIterator;
use SeekableIterator;

/**
 * Represents a paginable iterator. Since the {@see SeekableIterator} interface already allows the specification of an
 * {@see SeekableIterator::seek() offset} before iterating this one just focuses on limiting the results.
 *
 * @package App\Util\File\Support\Contracts
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
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
