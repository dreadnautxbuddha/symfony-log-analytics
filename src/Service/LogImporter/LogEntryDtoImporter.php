<?php

namespace App\Service\LogImporter;

use App\Entity;
use App\Dto\Entity\LogEntry\LogEntry as LogEntryDto;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Responsible for importing {@see LogEntryDto} objects as {@see \App\Entity\LogEntry} objects
 *
 * @package App\Service\LogImporter
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class LogEntryDtoImporter
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * Receives an array of {@see LogEntryDto} data transfer objects and saves them as {@see \App\Entity\LogEntry}
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

            $log_entry->setServiceName($log_entry_dto->getServiceName());
            $log_entry->setLoggedAt($log_entry_dto->getLoggedAt());
            $log_entry->setHttpRequestMethod($log_entry_dto->getHttpRequestMethod());
            $log_entry->setHttpRequestTarget($log_entry_dto->getHttpRequestTarget());
            $log_entry->setHttpProtocolVersion($log_entry_dto->getHttpVersion());
            $log_entry->setHttpStatusCode($log_entry_dto->getHttpStatusCode());

            $this->entityManager->persist($log_entry);
        }

        $this->entityManager->flush();
    }
}
