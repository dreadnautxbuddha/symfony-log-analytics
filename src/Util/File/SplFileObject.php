<?php

namespace App\Util\File;

use function is_null;

/**
 * @package App\Util\File
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class SplFileObject extends \SplFileObject
{
    /**
     * The number of rows to chunk the file into when iterating via {@see SplFileObject::each()}
     *
     * @var int|null
     */
    protected ?int $chunkSize = null;

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
        while (! $this->eof()) {
            if (is_null($this->chunkSize)) {
                $callback($this);

                continue;
            }

            $chunk = [];

            for ($i = 0; $i < $this->chunkSize; $i++) {
                $chunk[] = $this;

                $this->next();
            }

            $callback($chunk);
        }
    }
}
