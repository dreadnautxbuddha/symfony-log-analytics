<?php

namespace Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\Assembler;

use Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoInterface;
use Dreadnaut\LogAnalyticsBundle\EntityDto\Support\Contracts\EntityDtoAssemblerInterface;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use DateTimeImmutable;
use Exception;

use function array_filter;
use function array_values;
use function count;
use function intval;
use function preg_split;

use const PREG_SPLIT_DELIM_CAPTURE;

/**
 * @package Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\Assembler
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
final class FromString implements EntityDtoAssemblerInterface
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
    // phpcs:ignore
    public const string PATTERN = '/^([A-Z]+-SERVICE) - - \[(\d{2}\/(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\/\d{4}:\d{2}:\d{2}:\d{2} \+\d{4})\] "(GET|POST|PUT|DELETE|PATCH) ((?:https?:\/\/)?(?:[A-Za-z0-9.-]+)?(?:\/[\w\/?=&%\-#.]+)?) HTTP\/(\d\.\d)" ([1-5]\d{2})$/';

    /**
     * @inheritDoc
     */
    public function assemble(mixed $input): ?EntityDtoInterface
    {
        $segments = array_values(array_filter(preg_split(self::PATTERN, $input, -1, PREG_SPLIT_DELIM_CAPTURE)));

        if (count($segments) < 5) {
            return null;
        }

        [
            $serviceName,
            $loggedAt,
            $httpRequestMethod,
            $httpRequestTarget,
            $httpVersion,
            $httpStatusCode
        ] = $segments;

        try {
            // Wrapped the log entry creation in a try/catch clause to shut up PHPStorm. We're not going to encounter
            // DateTime errors here because we have explicitly declared the format in our {@see self::PATTERN pattern}.
            // If we were to get a datetime that violates that, we won't have the complete segments and will thus result
            // in this method returning `null`
            return new LogEntry(
                null,
                $serviceName,
                new DateTimeImmutable($loggedAt),
                RequestMethod::from($httpRequestMethod),
                $httpRequestTarget,
                $httpVersion,
                intval($httpStatusCode)
            );
        } catch (Exception) {
        }

        return null;
    }
}
