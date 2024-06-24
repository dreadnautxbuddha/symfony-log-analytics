<?php

namespace App\Dto\Entity\LogEntry\Assembler;

use App\Dto\Entity\LogEntry\LogEntry;
use App\Dto\Entity\Support\Contracts\EntityDtoInterface;
use App\Dto\Entity\Support\Contracts\EntityDtoAssemblerInterface;
use App\Enum\Http\RequestMethod;
use DateTimeImmutable;
use Exception;

use function array_filter;
use function array_values;
use function count;
use function preg_split;
use const PREG_SPLIT_DELIM_CAPTURE;

/**
 * @package App\Assembler\DTO\LogEntry
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class FromString implements EntityDtoAssemblerInterface
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
    public function assemble(): ?EntityDtoInterface
    {
        $segments = array_values(array_filter(preg_split(self::PATTERN, $this->input, -1, PREG_SPLIT_DELIM_CAPTURE)));

        if (count($segments) < 5) {
            return null;
        }

        [
            $service_name,
            $logged_at,
            $http_request_method,
            $http_request_target,
            $http_version,
            $http_status_code
        ] = $segments;

        try {
            // Wrapped the log entry creation in a try/catch clause to shut up PHPStorm. We're not going to encounter
            // DateTime errors here because we have explicitly declared the format in our {@see self::PATTERN pattern}.
            // If we were to get a datetime that violates that, we won't have the complete segments and will thus result
            // in this method returning `null`
            return new LogEntry(
                null,
                $service_name,
                new DateTimeImmutable($logged_at),
                RequestMethod::from($http_request_method),
                $http_request_target,
                $http_version,
                $http_status_code
            );
        } catch (Exception) {
        }

        return null;
    }
}
