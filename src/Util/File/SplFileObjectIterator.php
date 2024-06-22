<?php

namespace App\Util\File;

use Iterator;
use SplFileObject;

/**
 * An iterator that's primarily focused on an {@see SplFileObject} and adds the ability to chunk results when iterating
 * through its lines.
 *
 * @package App\Util\File
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class SplFileObjectIterator implements Iterator
{
    /**
     * The number of rows to chunk the file into when iterating via {@see SplFileObject::each()}
     *
     * @var int|null
     */
    protected ?int $chunkSize = null;

    public function __construct(protected SplFileObject $file)
    {
    }

    /**
     * Instruct the iterator to supply n number of lines to the callback that's called when iterating through the file
     * via {@see self::each()}
     *
     * @param int|null $size
     *
     * @return $this
     */
    public function chunk(?int $size = null): self
    {
        $this->chunkSize = $size;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @todo Test performance when the chunk size reaches upwards of thousands since we are passing this object n times.
     */
    public function current(): string|array|false
    {
        if ($this->chunkSize === null) {
            return $this->file->current();
        }

        $chunk = [];

        for ($i = 0; $i < $this->chunkSize; $i++) {
            $chunk[] = $this->file;

            $this->next();
        }

        return $chunk;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        if ($this->chunkSize === null) {
            $this->file->next();
        }

        for ($i = 0; $i < $this->chunkSize; $i++) {
            $this->file->next();
        }
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->file->key();
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->file->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->file->rewind();
    }
}
