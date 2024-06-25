<?php

namespace Dreadnaut\LogAnalyticsBundle\Command\Import\Log\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Used as input validation rules for the Log importer command. You'll notice that the {@see Cli::$offset},
 * {@see Cli::$limit}, and {@see Cli::$chunkSize} are all type-hinted with different types. We do this so that the
 * validator will not try and parse the incoming value into a type that will eventually not result in the error. ie.,
 * supplying a value of `0.1` to a type-hint of `int` will result in the value being read as `0`
 *
 * @package Dreadnaut\LogAnalyticsBundle\Command\Import\Log
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
readonly class Cli
{
    public function __construct(
        #[Assert\File(null, null, null, null, null, 'The file at the specified path could not be found.')]
        public ?string $path = null,

        #[Assert\Regex("/^\d*$/", 'The offset {{ value }} must be an integer.')]
        public string|int|float|null $offset = null,

        #[Assert\Regex("/^\d*$/", 'The limit {{ value }} must be an integer.')]
        public string|int|float|null $limit = null,

        #[Assert\Regex("/^\d*$/", 'The chunk size {{ value }} must be an integer.')]
        public string|int|float|null $chunkSize = null,
    )
    {
    }
}
