<?php

namespace Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter;

use Doctrine\ORM\EntityManagerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity;
use Dreadnaut\LogAnalyticsBundle\EntityDto;
use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface;

use function is_null;

/**
 * Responsible for importing {@see EntityDto\LogEntry\LogEntry} objects as
 * {@see \Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry} objects
 *
 * @package Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogEntryDtoImporter
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Entity\Support\Contracts\EntityAssemblerInterface $assembler
    ) {
    }

    /**
     * Receives an array of {@see LogEntryDto} data transfer objects and saves them as
     * {@see \Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry}
     * objects
     *
     * @param array<EntityDto\LogEntry\LogEntry|EntityDtoInterface> $logEntryDtos
     *
     * @return void
     */
    public function import(array $logEntryDtos): void
    {
        foreach ($logEntryDtos as $logEntryDto) {
            $logEntry = $this->assembler->assemble($logEntryDto);

            if (is_null($logEntry)) {
                continue;
            }

            $this->entityManager->persist($logEntry);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
