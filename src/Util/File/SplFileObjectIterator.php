<?php

namespace App\Util\File;

use Closure;
use RecursiveIterator;
use SeekableIterator;
use SplFileObject;

use function call_user_func;

/**
 * An iterator that's primarily focused on an {@see SplFileObject} and adds the ability to chunk results when iterating
 * through its lines.
 *
 * @package App\Util\File
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class SplFileObjectIterator implements RecursiveIterator, SeekableIterator
{
    /**
     * The number of rows to chunk the file into when iterating via {@see SplFileObject::each()}
     *
     * @var int|null
     */
    protected ?int $chunkSize = null;

    /**
     * The zero-based line number to seek to, and is set to `0` by default signifying that reading should start from the
     * first line.
     *
     * @var int|null
     */
    protected ?int $offset = 0;

    /**
     * The maximum number of rows to yield, and is set to the total number of lines in the file by default signifying
     * that all lines should be returned.
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * @var Closure
     */
    protected Closure $chunkCallback;

    public function __construct(protected readonly SplFileObject $file)
    {
    }

    /**
     * Returns the underlying {@see SplFileObject} object, useful when iterating through it with while loop
     *
     * @return SplFileObject
     */
    public function getFile(): SplFileObject
    {
        return $this->file;
    }

    /**
     * Instruct the iterator to supply n number of lines to the loop (be it a while loop or a foreach) that's called
     * when iterating through the {@see SplFileObject}. Each item in the chunk will then be run through the callback,
     * giving you the ability to choose which information about the current line you want to get. By default, the
     * callback used will just return the value of {@see SplFileObject::fgets()}.
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
    public function chunk(?int $size = null, ?Closure $callback = null): void
    {
        $this->chunkSize = $size;
        $this->chunkCallback = $callback ?? fn (SplFileObject $file) => $file->fgets();
    }

    /**
     * Limits the number of lines to be yielded.
     *
     * @param int|null $limit
     *
     * @return void
     */
    public function limit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritDoc}
     *
     * When chunking, the returned value will be an array of lines.
     *
     * @todo Test performance when the chunk size reaches upwards of thousands since we are passing this object n times.
     */
    public function current(): string|array|false|SplFileObject
    {
        if ($this->chunkSize === null) {
            return $this->getFile();
        }

        $chunk = [];
        $activeChunkSize = 0;
        // Since this iterator is stateful, we can always rely on the valid() method to check if the pointer is still
        // valid after running getFile() which may or may not always move the cursor.
        while ($this->valid() && $activeChunkSize < $this->chunkSize) {
            $chunk[] = call_user_func($this->chunkCallback, $this->getFile());

            // We can completely omit this in favor of just counting the $chunk array, but for performance purposes, we
            // are going to stick with the good 'ol indices.
            $activeChunkSize++;
        }

        return $chunk;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->getFile()->next();
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->getFile()->key();
    }

    /**
     * {@inheritDoc}
     *
     * This method is run first before {@see self::current()} is executed and is used to determine whether it will be
     * read or not
     */
    public function valid(): bool
    {
        return ! $this->hasReachedLineLimit() && $this->getFile()->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->getFile()->rewind();
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset): void
    {
        $this->offset = $offset;

        $this->getFile()->seek($offset);
    }

    /**
     * @inheritDoc
     */
    public function hasChildren(): bool
    {
        return $this->getFile()->hasChildren();
    }

    /**
     * @inheritDoc
     */
    public function getChildren(): ?RecursiveIterator
    {
        return $this->getFile()->getChildren();
    }

    /**
     * Determines whether the iterator has reached the user's defined limits or not.
     *
     * @return bool
     */
    private function hasReachedLineLimit(): bool
    {
        return $this->limit !== null && ($this->key() - $this->offset) >= $this->limit;

    }
}
