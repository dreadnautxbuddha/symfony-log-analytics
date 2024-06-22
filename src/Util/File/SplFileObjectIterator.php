<?php

namespace App\Util\File;

use SplFileObject;

use function is_null;

/**
 * An object that's used to iterate through an {@see SplFileObject} in a much more fluent manner.
 *
 * @package App\Util\File
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class SplFileObjectIterator
{
    public function __construct(protected SplFileObject $file)
    {
    }

    /**
     * The number of rows to chunk the file into when iterating via {@see SplFileObject::each()}
     *
     * @var int|null
     */
    protected ?int $chunkSize = null;

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
     * Iterates through each line in the file, and passes the current object's instance to the supplied callback,
     * allowing us to call, for example, {@see SplFileObject::fgets()} on each.
     *
     * @todo Test performance when the chunk size reaches upwards of thousands since we are passing this object n times.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function each(callable $callback): void
    {
        while (! $this->file->eof()) {
            if (is_null($this->chunkSize)) {
                $callback($this->file);

                continue;
            }

            $chunk = [];

            for ($i = 0; $i < $this->chunkSize; $i++) {
                $chunk[] = $this->file;

                $this->file->next();
            }

            $callback($chunk);
        }
    }
}
