<?php

namespace Dreadnaut\LogAnalyticsBundle\Util\File\Support\Contracts;

use RecursiveIterator;
use SeekableIterator;

/**
 * Represents a paginable iterator. Since the {@see SeekableIterator} interface already allows the specification of an
 * {@see SeekableIterator::seek() offset} before iterating this one just focuses on limiting the results.
 *
 * @package Dreadnaut\LogAnalyticsBundle\Util\File\Support\Contracts
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
interface PaginableIteratorInterface extends RecursiveIterator, SeekableIterator
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
