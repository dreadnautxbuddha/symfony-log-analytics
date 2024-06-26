<?php

namespace Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts;

use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface;

/**
 * An assembler that is responsible for transforming a
 * {@see \Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface dto} into an
 * {@see EntityInterface entity}
 *
 * @package Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
interface EntityAssemblerInterface
{
    /**
     * Creates an entity out of the supplied DTO
     *
     * @param EntityDtoInterface $entityDto
     *
     * @return EntityInterface|null
     */
    public function assemble(EntityDtoInterface $entityDto): ?EntityInterface;
}
