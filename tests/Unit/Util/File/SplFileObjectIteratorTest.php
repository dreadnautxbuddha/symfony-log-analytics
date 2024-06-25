<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Unit\Util\File;

use PHPUnit\Framework\TestCase;
use Dreadnaut\LogAnalyticsBundle\Util\File\SplFileObjectIteratorInterface;
use SplFileObject;

use function array_map;
use function trim;

class SplFileObjectIteratorTest extends TestCase
{
    public function testIteration_whenChunkSizeIsSet_shouldChunkLines()
    {
        $chunks = [];
        $iterable = new SplFileObjectIteratorInterface(new SplFileObject('tests/Data/Service/LogFileImporter/logs.log'));
        $iterable->chunk(2);

        foreach ($iterable as $lines) {
            $chunks[] = array_map(fn(string $line) => trim($line), $lines);
        }

        $this->assertEquals(
            [
                [
                    'USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201',
                    'USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400',
                ],
                [
                    'INVOICE-SERVICE - - [17/Aug/2018:09:21:55 +0000] "POST /invoices HTTP/1.1" 201',
                    'USER-SERVICE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [17/Aug/2018:09:21:57 +0000] "POST /users HTTP/1.1" 201',
                    'INVOICE-SERVICE - - [17/Aug/2018:09:22:58 +0000] "POST /invoices HTTP/1.1" 201',
                ],
                [
                    'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST /invoices HTTP/1.1" 400',
                    'INVOICE-SERVICE - - [17/Aug/2018:09:23:53 +0000] "POST /invoices HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [17/Aug/2018:09:23:54 +0000] "POST /users HTTP/1.1" 400',
                    'USER-SERVICE - - [17/Aug/2018:09:23:55 +0000] "POST /users HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [17/Aug/2018:09:26:51 +0000] "POST /users HTTP/1.1" 201',
                    'INVOICE-SERVICE - - [17/Aug/2018:09:26:53 +0000] "POST /invoices HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [17/Aug/2018:09:29:11 +0000] "POST /users HTTP/1.1" 201',
                    'USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] "POST /users HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [18/Aug/2018:09:30:54 +0000] "POST /users HTTP/1.1" 400',
                    'USER-SERVICE - - [18/Aug/2018:09:31:55 +0000] "POST /users HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [18/Aug/2018:09:31:56 +0000] "POST /users HTTP/1.1" 201',
                    'INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [18/Aug/2018:10:32:56 +0000] "POST /users HTTP/1.1" 201',
                    'USER-SERVICE - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/1.1" 201',
                ],
                [
                    '"POST /users HTTP/1.1" 201',
                    '',
                ],

            ],
            $chunks
        );
    }

    public function testIteration_whenChunkSizeIsNotSet_shouldChunkLines()
    {
        $chunks = [];
        $iterable = new SplFileObjectIteratorInterface(new SplFileObject('tests/Data/Service/LogFileImporter/logs.log'));

        foreach ($iterable as $line) {
            $chunks[] = trim($line->fgets());
        }

        $this->assertEquals(
            [
                'USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400',
                'INVOICE-SERVICE - - [17/Aug/2018:09:21:55 +0000] "POST /invoices HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:21:57 +0000] "POST /users HTTP/1.1" 201',
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:58 +0000] "POST /invoices HTTP/1.1" 201',
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST /invoices HTTP/1.1" 400',
                'INVOICE-SERVICE - - [17/Aug/2018:09:23:53 +0000] "POST /invoices HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:23:54 +0000] "POST /users HTTP/1.1" 400',
                'USER-SERVICE - - [17/Aug/2018:09:23:55 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:26:51 +0000] "POST /users HTTP/1.1" 201',
                'INVOICE-SERVICE - - [17/Aug/2018:09:26:53 +0000] "POST /invoices HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:29:11 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [18/Aug/2018:09:30:54 +0000] "POST /users HTTP/1.1" 400',
                'USER-SERVICE - - [18/Aug/2018:09:31:55 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [18/Aug/2018:09:31:56 +0000] "POST /users HTTP/1.1" 201',
                'INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices HTTP/1.1" 201',
                'USER-SERVICE - - [18/Aug/2018:10:32:56 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/1.1" 201',
                '"POST /users HTTP/1.1" 201',
                '',
            ],
            $chunks
        );
    }

    public function testIteration_whenOffsetIsSupplied_shouldOnlyReadFromThatLine()
    {
        $chunks = [];
        $iterable = new SplFileObjectIteratorInterface(new SplFileObject('tests/Data/Service/LogFileImporter/logs.log'));
        $iterable->seek(19);

        while ($iterable->valid()) {
            $chunks[] = trim($iterable->current()->fgets());
        }

        $this->assertEquals(
            [
                'USER-SERVICE - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/1.1" 201',
                '"POST /users HTTP/1.1" 201',
                '',
            ],
            $chunks
        );
    }

    public function testIteration_whenLimitIsSupplied_shouldOnlyReadUntilThatLine()
    {
        $lines = [];
        $iterable = new SplFileObjectIteratorInterface(new SplFileObject('tests/Data/Service/LogFileImporter/logs.log'));
        $iterable->limit(2);

        while ($iterable->valid()) {
            $lines[] = trim($iterable->current()->fgets());
        }

        $this->assertEquals(
            [
                'USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201',
                'USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400',
            ],
            $lines
        );
    }

    public function testIteration_whenLimitAndOffsetIsSupplied_shouldReturnCorrectRows()
    {
        $lines = [];
        $iterable = new SplFileObjectIteratorInterface(new SplFileObject('tests/Data/Service/LogFileImporter/logs.log'));
        $iterable->seek(17);
        $iterable->limit(2);

        while ($iterable->valid()) {
            $lines[] = trim($iterable->current()->fgets());
        }

        $this->assertEquals(
            [
                'INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices HTTP/1.1" 201',
                'USER-SERVICE - - [18/Aug/2018:10:32:56 +0000] "POST /users HTTP/1.1" 201',
            ],
            $lines
        );
    }

    public function testIteration_whenLimitOffsetAndChunkIsSupplied_shouldReturnCorrectRows()
    {
        $chunks = [];
        $iterable = new SplFileObjectIteratorInterface(new SplFileObject('tests/Data/Service/LogFileImporter/logs.log'));
        $iterable->seek(5);
        $iterable->chunk(3);
        $iterable->limit(6);

        while ($iterable->valid()) {
            $chunks[] = array_map(fn(string $line) => trim($line), $iterable->current());
        }

        $this->assertEquals(
            [
                [
                    'INVOICE-SERVICE - - [17/Aug/2018:09:22:58 +0000] "POST /invoices HTTP/1.1" 201',
                    'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST /invoices HTTP/1.1" 400',
                    'INVOICE-SERVICE - - [17/Aug/2018:09:23:53 +0000] "POST /invoices HTTP/1.1" 201',
                ],
                [
                    'USER-SERVICE - - [17/Aug/2018:09:23:54 +0000] "POST /users HTTP/1.1" 400',
                    'USER-SERVICE - - [17/Aug/2018:09:23:55 +0000] "POST /users HTTP/1.1" 201',
                    'USER-SERVICE - - [17/Aug/2018:09:26:51 +0000] "POST /users HTTP/1.1" 201',
                ],
            ],
            $chunks
        );
    }
}
