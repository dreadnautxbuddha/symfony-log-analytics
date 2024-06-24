<?php

namespace App\Dto\Entity\LogEntry;

use App\Dto\Entity\Support\Contracts\EntityDtoInterface;
use App\Enum\Http\RequestMethod;
use DateTimeImmutable;

/**
 * A data transfer object that can be converted directly into a {@see \App\Entity\LogEntry} entity.
 *
 * @package App\Dto\Entity\LogEntry
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
readonly class LogEntry implements EntityDtoInterface
{
    /**
     * @param int|null                $id
     * @param string|null             $serviceName
     * @param DateTimeImmutable|null $loggedAt
     * @param RequestMethod|null      $httpRequestMethod
     * @param string|null             $httpRequestTarget
     * @param string|null             $httpVersion
     * @param int|null                $httpStatusCode
     */
    public function __construct(
        public ?int $id = null,
        public ?string $serviceName = null,
        public ?DateTimeImmutable $loggedAt = null,
        public ?RequestMethod $httpRequestMethod = null,
        public ?string $httpRequestTarget = null,
        public ?string $httpVersion = null,
        public ?int $httpStatusCode = null,
    )
    {
    }
}
