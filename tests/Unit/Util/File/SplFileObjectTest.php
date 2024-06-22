<?php

namespace Tests\Unit\Util\File;

use PHPUnit\Framework\TestCase;
use App\Util\File\SplFileObject;

use function array_map;

class SplFileObjectTest extends TestCase
{
    public function testChunk_whenNumberOfLinesExceedChunk_shouldChunkLines()
    {
        $file = new SplFileObject('tests/Data/Service/LogFileImporter/logs.log');
        $chunks = [];

        $file->chunk(2)->each(function (array $lines) use (&$chunks) {
            $chunks[] = array_map(fn(SplFileObject $line) => trim($line->fgets()), $lines);
        });

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
}
