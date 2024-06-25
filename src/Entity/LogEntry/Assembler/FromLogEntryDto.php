<?php

namespace Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\Assembler;

use Dreadnaut\LogAnalyticsBundle\Entity;
use Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts\EntityAssemblerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts\EntityInterface;
use Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface;

/**
 * Maps an {@see EntityDtoInterface entity dto} into an {@see EntityInterface entity}
 *
 * @package Dreadnaut\LogAnalyticsBundle\Entity\Assembler\LogEntry
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
final class FromLogEntryDto implements EntityAssemblerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param LogEntry $entityDto
     */
    public function assemble(EntityDtoInterface $entityDto): ?EntityInterface
    {
        $logEntry = new Entity\LogEntry\LogEntry();

        $logEntry
            ->setServiceName($entityDto->serviceName)
            ->setLoggedAt($entityDto->loggedAt)
            ->setHttpRequestMethod($entityDto->httpRequestMethod)
            ->setHttpRequestTarget($entityDto->httpRequestTarget)
            ->setHttpVersion($entityDto->httpVersion)
            ->setHttpStatusCode($entityDto->httpStatusCode);

        return $logEntry;
    }
}
