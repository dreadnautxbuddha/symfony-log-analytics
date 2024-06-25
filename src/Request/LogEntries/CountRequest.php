<?php

namespace Dreadnaut\LogAnalyticsBundle\Request\LogEntries;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Used in validating the request to
 * {@see \Dreadnaut\LogAnalyticsBundle\Controller\LogEntries\CountController}
 *
 * @package Dreadnaut\LogAnalyticsBundle\Request\LogEntries
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class CountRequest
{
    /**
     * @param array<string> $serviceNames
     * @param string|null   $startDate
     * @param string|null   $endDate
     * @param int|null      $statusCode
     *
     * @todo Make {@see CountRequest::$serviceNames} and {@see CountRequest::$statusCode} readonly. Had to remove
     *      temporarily to make tests pass
     */
    public function __construct(
        #[Assert\Type('array')]
        public array $serviceNames = [],
        #[Assert\DateTime]
        public readonly ?string $startDate = null,
        #[Assert\DateTime]
        public readonly ?string $endDate = null,
        #[Assert\Range(min: 100, max: 599)]
        public ?int $statusCode = null,
    ) {
    }
}
