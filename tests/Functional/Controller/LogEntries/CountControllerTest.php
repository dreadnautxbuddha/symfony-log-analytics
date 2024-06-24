<?php

namespace App\Tests\Functional\Controller\LogEntries;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function http_build_query;
use function json_decode;

class CountControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function testController_whenAccessed_shouldReturn200(): void
    {
        $this->client->request('GET', '/logs/count');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testController_whenStartDateIsInvalid_shouldReturn422()
    {
        $data = http_build_query(['startDate' => 'an invalid date']);

        $this->client->jsonRequest('GET', "/logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => [
                    'startDate' => [
                        'This value is not a valid datetime.',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function testController_whenEndDateIsInvalid_shouldReturn422()
    {
        $data = http_build_query(['endDate' => 'an invalid date']);

        $this->client->request('GET', "logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => [
                    'endDate' => [
                        'This value is not a valid datetime.',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * @dataProvider invalidHttpStatusCodes
     */
    public function testController_whenHttpStatusCodeIsInvalid_shouldReturn422(string|int $invalidStatusCode)
    {
        $data = http_build_query(['statusCode' => $invalidStatusCode]);

        $this->client->request('GET', "/logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => [
                    'statusCode' => [
                        'This value should be between 100 and 599.',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function testController_whenHttpStatusCodeIsNotInteger_shouldReturn422()
    {
        $data = http_build_query(['statusCode' => 'a string']);

        $this->client->request('GET', "logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => [
                    'statusCode' => [
                        'This value should be a valid number.',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }


    public static function invalidHttpStatusCodes(): array
    {
        return [
            [99],
            [600],
        ];
    }
}
