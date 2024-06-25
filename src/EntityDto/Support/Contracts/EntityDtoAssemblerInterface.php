<?php

namespace Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts;

/**
 * An assembler is responsible for transforming input from various data sources into data transfer objects.
 *
 * @package Dreadnaut\LogAnalyticsBundle\EntityDto\Interface
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
interface EntityDtoAssemblerInterface
{
    /**
     * Creates an {@see EntityDtoInterface} object out of supplied input. If one cannot be made, due to, let's say data
     * constraint errors, `null` will be returned.
     *
     * @param mixed $input
     *
     * @return EntityDtoInterface|null
     */
    public function assemble(mixed $input): ?EntityDtoInterface;
}
