<?php

namespace App\Util\File;

use App\Util\File\Support\Contracts;
use Closure;
use RecursiveIterator;
use SplFileObject;

use function call_user_func;
use function is_null;

/**
 * An iterator that's primarily focused on an {@see SplFileObject} and adds the ability to chunk results when iterating
 * through its lines.
 *
 * @package App\Util\File
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class SplFileObjectIterator implements Contracts\ChunkableIterator, Contracts\PaginableIterator
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
     * When chunking iteration results, each item in it will be run through this callback, giving you the ability to
     * choose which information about the current line you want to get. By default, the callback used will just return
     * the value of {@see SplFileObject::fgets()}.
     *
     * This callback, when run, should ALWAYS move the pointer to the next item in {@see SplFileObjectIterator::$file}
     * to ensure that we are always getting the right line.
     *
     * @var Closure
     */
    protected Closure $getChunkItemDataCallback;

    public function __construct(protected readonly SplFileObject $file)
    {
    }

    /**
     * @inheritDoc
     */
    public function chunk(?int $size = null, ?Closure $callback = null): void
    {
        $this->chunkSize = $size;

        if (is_null($callback)) {
            return;
        }

        $this->getChunkItemDataCallback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function getChunkItemData(SplFileObject $file): string
    {
        return $file->fgets();
    }

    /**
     * @inheritDoc
     */
    public function limit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritDoc}
     *
     * When chunking, the returned value will be a generator whose data will contain
     * {@see self::$getChunkItemDataCallback information about the chunk}.
     *
     * @todo Test performance when the chunk size reaches upwards of thousands since we are passing this object n times.
     */
    public function current(): string|array|false|SplFileObject
    {
        if ($this->chunkSize === null) {
            return $this->file;
        }

        $chunk = [];
        $active_chunk_size = 0;
        // Since this iterator is stateful, we can always rely on the valid() method to check if the pointer is still
        // valid after running the chunk item data callback which may or may not always move the cursor.
        while ($this->valid() && $active_chunk_size < $this->chunkSize) {
            $chunk[] = call_user_func($this->getChunkItemDataCallback ?? [$this, 'getChunkItemData'], $this->file);

            // We can completely omit this in favor of just counting the $chunk array, but for performance purposes, we
            // are going to stick with the good 'ol indices.
            $active_chunk_size++;
        }

        return $chunk;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->file->next();
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->file->key();
    }

    /**
     * {@inheritDoc}
     *
     * This method is run first before {@see self::current()} is executed and is used to determine whether it will be
     * read or not
     */
    public function valid(): bool
    {
        return ! $this->hasReachedLineLimit() && $this->file->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->file->rewind();
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset): void
    {
        $this->offset = $offset;

        $this->file->seek($offset);
    }

    /**
     * @inheritDoc
     */
    public function hasChildren(): bool
    {
        return $this->file->hasChildren();
    }

    /**
     * @inheritDoc
     */
    public function getChildren(): ?RecursiveIterator
    {
        return $this->file->getChildren();
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
