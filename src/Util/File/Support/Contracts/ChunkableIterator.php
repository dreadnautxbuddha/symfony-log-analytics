<?php

namespace App\Util\File\Support\Contracts;

use Closure;
use RecursiveIterator;
use SeekableIterator;
use SplFileObject;

interface ChunkableIterator extends RecursiveIterator, SeekableIterator
{
    /**
     * Instruct the iterator to supply n number of lines to the loop (be it a while loop or a foreach) that's called
     * when iterating through the {@see SplFileObject}.
     *
     * Note: Beware of overriding the callback with either one that moves the inner cursor to the next, or having one
     * that DOES NOT move the cursor at all because this might have unexpected results. By default,
     * {@see SplFileObject::fgets()} moves the cursor to the next once run.
     *
     * @param int|null     $size
     * @param Closure|null $callback
     *
     * @return void
     */
    public function chunk(?int $size = null, ?Closure $callback = null): void;

    /**
     * When chunking iteration results, each item in it will be run through this callback, giving you the ability to
     * choose which information about the current line you want to get. By default, the callback used will just return
     * the value of {@see SplFileObject::fgets()}.
     *
     * This callback, when run, should ALWAYS move the pointer to the next item in the file to ensure that we are always
     * getting the right line.
     *
     * @param SplFileObject $file
     *
     * @return string
     */
    public function getChunkItemData(SplFileObject $file): string;
}
