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
readonly class CountRequest
{
    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $statusCode
     */
    public function __construct(
        #[Assert\DateTime]
        public ?string $startDate = null,
        #[Assert\DateTime]
        public ?string $endDate = null,
        #[Assert\Range(min: 100, max: 599)]
        public ?string $statusCode = null
    )
    {
    }
}
