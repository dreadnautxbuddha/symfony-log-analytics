<?php

namespace Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter;

use Dreadnaut\LogAnalyticsBundle\Entity;
use Dreadnaut\LogAnalyticsBundle\Dto\Entity\LogEntry\LogEntry as LogEntryDto;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Responsible for importing {@see LogEntryDto} objects as {@see \Dreadnaut\LogAnalyticsBundle\Entity\LogEntry} objects
 *
 * @package Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogEntryDtoImporter
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * Receives an array of {@see LogEntryDto} data transfer objects and saves them as
     * {@see \Dreadnaut\LogAnalyticsBundle\Entity\LogEntry}
     * objects
     *
     * @param array<LogEntryDto> $log_entry_dtos
     *
     * @return void
     */
    public function import(array $log_entry_dtos): void
    {
        foreach ($log_entry_dtos as $log_entry_dto) {
            $log_entry = new Entity\LogEntry();

            $log_entry
                ->setServiceName($log_entry_dto->serviceName)
                ->setLoggedAt($log_entry_dto->loggedAt)
                ->setHttpRequestMethod($log_entry_dto->httpRequestMethod)
                ->setHttpRequestTarget($log_entry_dto->httpRequestTarget)
                ->setHttpVersion($log_entry_dto->httpVersion)
                ->setHttpStatusCode($log_entry_dto->httpStatusCode);

            $this->entityManager->persist($log_entry);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
