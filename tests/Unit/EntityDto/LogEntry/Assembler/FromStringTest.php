<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Unit\EntityDto\LogEntry\Assembler;

use Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\Assembler\FromString;
use PHPUnit\Framework\TestCase;

class FromStringTest extends TestCase
{
    /**
     * @dataProvider invalidInput
     */
    // phpcs:ignore
    public function testAssemble_whenInputIsInvalid_shouldReturnNull(string $input)
    {
        $assembler = new FromString();

        $entityDto = $assembler->assemble($input);

        $this->assertNull($entityDto);
    }

    /**
     * @dataProvider validUrls
     */
    // phpcs:ignore
    public function testAssemble_whenRequestTargetIsAValidUrl_shouldReturnEntityDto(
        string $input,
        string $expectedRequestTarget
    ) {
        $assembler = new FromString();

        /** @var \Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\LogEntry $entityDto */
        $entityDto = $assembler->assemble($input);

        $this->assertEquals($expectedRequestTarget, $entityDto->httpRequestTarget);
    }

    public static function invalidInput(): array
    {
        return [
            ['USER-SERVICE- - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201'],
            ['USER-SERVICE -- [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400'],
            ['INVOICE-SERVICE - -[17/Aug/2018:09:21:55 +0000] "POST /invoices HTTP/1.1" 201'],
            ['USER-SERVICE - - [invalid date] "POST /users HTTP/1.1" 201'],
            ['USER-SERVICE - - [17/Aug/2018:09:21:57 +0000]"POST /users HTTP/1.1" 201'],
            ['INVOICE-SERVICE - - [17/Aug/2018:09:22:58 +0000] "POST/invoices HTTP/1.1" 201'],
            ['INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST /invoicesHTTP/1.1"400'],
            ['INVOICE-SERVICE - - [17/Aug/2018:09:23:53 +0000] "POST /invoices HTTP/1.1" 999'],
            ['USER-SERVICE - - [17/Aug/2018:09:23:54 +0000] "POST /users HTTP/1.1" invalidstatuscode'],
            ['    - - [17/Aug/2018:09:23:55 +0000] "POST /users HTTP/1.1" 201'],
            ['USER-SERVICE - [17/Aug/2018:09:26:51 +0000] "POST /users HTTP/1.1" 201'],
            ['INVOICE-SERVICE - - 17/Aug/2018:09:26:53 +0000] "POST /invoices HTTP/1.1" 201'],
            ['USER-SERVICE - - [17/Aug/2018:09:29:11 +0000 "POST /users HTTP/1.1" 201'],
            ['USER-SERVICE - - [17/Aug/2018:09:29:13 +0000] POST /users HTTP/1.1" 201'],
            ['USER-SERVICE - - [18/Aug/2018:09:30:54 +0000] \'POST /users HTTP/1.1" 400'],
            ['USER-SERVICE - - [18/Aug/2018:09:31:55 +0000] "POST /users HTTP/1.1 201'],
            ['USER-SERVICE - - [18/Aug/2018:09:31:56 +0000] "POST /users HTTP/1.1\' 201'],
            ['INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices /1.1" 201'],
            ['USER-SERVICE - - [18/Aug/2018:10:32:56 +0000] "POST /users HTTP" 201'],
            ['USER-SERVICE - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/" 201'],
            ['USER-SERVICE - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/1.1"'],
            ['USER - - [18/Aug/2018:10:33:59 +0000] "POST /users HTTP/1.1" 201'],
            ['USER-SERVICE'],
            ['- -'],
            ['[18/Aug/2018:10:33:59 +0000]'],
            ['"POST /users HTTP/1.1" 201'],
            ['201'],
            // phpcs:ignore
            ['INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices HTTP/1.1" 201 INVOICE-SERVICE - - [18/Aug/2018:10:26:53 +0000] "POST /invoices HTTP/1.1" 201'],
        ];
    }

    public static function validUrls(): array
    {
        return [
            [
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST /my/test/endpoint HTTP/1.1" 400',
                '/my/test/endpoint',
            ],
            [
                // phpcs:ignore
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST /my/test/endpoint?query1=value1&query2=value2 HTTP/1.1" 400',
                '/my/test/endpoint?query1=value1&query2=value2'
            ],
            [
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST https://www.google.com HTTP/1.1" 400',
                'https://www.google.com'
            ],
            [
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST http://www.google.com HTTP/1.1" 400',
                'http://www.google.com'
            ],
            [
                'INVOICE-SERVICE - - [17/Aug/2018:09:22:59 +0000] "POST http://hub.blogspot.com/#posts HTTP/1.1" 400',
                'http://hub.blogspot.com/#posts'
            ],
        ];
    }
}
