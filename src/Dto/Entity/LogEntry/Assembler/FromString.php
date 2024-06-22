<?php

namespace App\Dto\Entity\LogEntry\Assembler;

use App\Dto\Entity\LogEntry\LogEntry;
use App\Dto\Entity\Support\Contracts\EntityDto;
use App\Dto\Entity\Support\Contracts\EntityDtoAssembler;
use App\Enum\Http\RequestMethod;
use DateTimeImmutable;

use function array_filter;
use function array_values;
use function preg_split;
use const PREG_SPLIT_DELIM_CAPTURE;

/**
 * @package App\Assembler\DTO\LogEntry
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class FromString implements EntityDtoAssembler
{
    /**
     * The pattern we're using to ensure that an input looks somewhat like this:
     *
     * USER-SERVICE - - [18/Aug/2018:09:30:54 +0000] "POST /users HTTP/1.1" 400
     *
     * gets broken down into the following segments:
     *
     * 1. Service name
     * 2. Log datetime
     * 3. HTTP request method
     * 4. HTTP request target
     * 5. HTTP version
     * 6. HTTP status code
     */
    const string PATTERN = '/^([A-Z]+-SERVICE) - - \[(\d{2}\/(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\/\d{4}:\d{2}:\d{2}:\d{2} \+\d{4})\] "(GET|POST|PUT|DELETE|PATCH) (\/[\w\/]*) HTTP\/(\d\.\d)" ([1-5]\d{2})$/';

    public function __construct(protected string $input)
    {
    }

    /**
     * @inheritDoc
     */
    public function assemble(): ?EntityDto
    {
        [
            $service_name,
            $logged_at,
            $http_request_method,
            $http_request_target,
            $http_version,
            $http_status_code
        ] = array_values(array_filter(preg_split(self::PATTERN, $this->input, -1, PREG_SPLIT_DELIM_CAPTURE)));

        return new LogEntry(
            null,
            $service_name,
            new DateTimeImmutable($logged_at),
            RequestMethod::from($http_request_method),
            $http_request_target,
            $http_version,
            $http_status_code
        );
    }
}
