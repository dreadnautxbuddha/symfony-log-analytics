<?php

namespace App\Dto\Entity\LogEntry;

use App\Dto\Entity\Support\Contracts\EntityDto;
use App\Enum\Http\RequestMethod;

/**
 * A data transfer object that can be converted directly into a {@see \App\Entity\LogEntry} entity.
 *
 * @package App\Dto\Entity\LogEntry
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
readonly class LogEntry implements EntityDto
{
    /**
     * @param int|null                $id
     * @param string|null             $serviceName
     * @param \DateTimeImmutable|null $loggedAt
     * @param RequestMethod|null      $httpRequestMethod
     * @param string|null             $httpRequestTarget
     * @param string|null             $httpVersion
     * @param int|null                $httpStatusCode
     */
    public function __construct(
        private ?int $id = null,
        private ?string $serviceName = null,
        private ?\DateTimeImmutable $loggedAt = null,
        private ?RequestMethod $httpRequestMethod = null,
        private ?string $httpRequestTarget = null,
        private ?string $httpVersion = null,
        private ?int $httpStatusCode = null,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function getLoggedAt(): ?\DateTimeImmutable
    {
        return $this->loggedAt;
    }

    public function getHttpRequestMethod(): ?RequestMethod
    {
        return $this->httpRequestMethod;
    }

    public function getHttpRequestTarget(): ?string
    {
        return $this->httpRequestTarget;
    }

    public function getHttpVersion(): ?string
    {
        return $this->httpVersion;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }
}
