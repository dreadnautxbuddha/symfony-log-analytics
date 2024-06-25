<?php

namespace Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry;

use DateTimeImmutable;
use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;

/**
 * A data transfer object that can be converted directly into a
 * {@see \Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry}
 * entity.
 *
 * @package Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry
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
