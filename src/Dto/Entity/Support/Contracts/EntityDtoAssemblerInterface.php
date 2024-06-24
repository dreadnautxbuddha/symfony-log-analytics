<?php

namespace App\Dto\Entity\Support\Contracts;

/**
 * An assembler is responsible for transforming input from various data sources into data transfer objects.
 *
 * @package App\Dto\Entity\Interface
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
interface EntityDtoAssemblerInterface
{
    /**
     * Creates an {@see EntityDtoInterface} object out of the data supplied to the assembler. If one cannot be made, due
     * to, let's say data constraint errors, `null` will be returned.
     *
     * @return EntityDtoInterface|null
     */
    public function assemble(): ?EntityDtoInterface;
}
