<?php

namespace Dreadnaut\LogAnalyticsBundle\Entity\Assembler\LogEntry;

use Dreadnaut\LogAnalyticsBundle\Entity;
use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\Assembler\Support\Contracts\EntityAssemblerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts\EntityInterface;

/**
 * Maps an {@see EntityDtoInterface entity dto} into an {@see EntityInterface entity}
 *
 * @package Dreadnaut\LogAnalyticsBundle\Entity\Assembler\LogEntry
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class FromLogEntryDto implements EntityAssemblerInterface
{
    /**
     * @inheritDoc
     */
    public function assemble(EntityDtoInterface $entityDto): ?EntityInterface
    {
        $log_entry = new Entity\LogEntry();

        $log_entry
            ->setServiceName($entityDto->serviceName)
            ->setLoggedAt($entityDto->loggedAt)
            ->setHttpRequestMethod($entityDto->httpRequestMethod)
            ->setHttpRequestTarget($entityDto->httpRequestTarget)
            ->setHttpVersion($entityDto->httpVersion)
            ->setHttpStatusCode($entityDto->httpStatusCode);

        return $log_entry;
    }
}
