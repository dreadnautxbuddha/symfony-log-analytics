<?php

namespace App\Request\LogEntries;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Used in validating the request to {@see \App\Controller\LogEntries\CountController}
 *
 * @package App\Request\LogEntries
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class CountRequest
{
    /**
     * @todo Make {@see CountRequest::$serviceNames} readonly. Had to remove temporarily to make tests pass
     *
     * @param array $serviceNames
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $statusCode
     */
    public function __construct(
        #[Assert\Type('array')]
        public array $serviceNames = [],

        #[Assert\DateTime]
        public readonly ?string $startDate = null,

        #[Assert\DateTime]
        public readonly ?string $endDate = null,

        #[Assert\Range(min: 100, max: 599)]
        public readonly ?string $statusCode = null,
    ) {
    }
}
