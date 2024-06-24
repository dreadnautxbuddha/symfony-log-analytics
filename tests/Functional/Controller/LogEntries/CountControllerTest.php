<?php

namespace App\Tests\Functional\Controller\LogEntries;

use App\Entity\LogEntry;
use App\Enum\Http\RequestMethod;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function http_build_query;
use function json_decode;

class CountControllerTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
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

    public function testController_whenQueryingByStartDate_shouldOnlyReturnLogEntriesAfterThatDate()
    {
        $past_log_entry = new LogEntry();
        $past_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:13'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($past_log_entry);
        $current_log_entry = new LogEntry();
        $current_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($current_log_entry);
        $future_log_entry = new LogEntry();
        $future_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($future_log_entry);
        $another_future_log_entry = new LogEntry();
        $another_future_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:16'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($another_future_log_entry);
        $this->entityManager->flush();
        $data = http_build_query(['startDate' => '2024-06-30 22:15:14']);

        $this->client->request('GET', "logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'count' => 3,
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function testController_whenQueryingByEndDate_shouldOnlyReturnLogEntriesBeforeThatDate()
    {
        $past_log_entry = new LogEntry();
        $past_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:12'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($past_log_entry);
        $current_log_entry = new LogEntry();
        $current_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($current_log_entry);
        $future_log_entry = new LogEntry();
        $future_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($future_log_entry);
        $this->entityManager->flush();
        $data = http_build_query(['endDate' => '2024-06-30 22:15:14']);

        $this->client->request('GET', "logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'count' => 2,
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function testController_whenQueryingByStatusCode_shouldOnlyReturnLogEntriesWithTheSpecifiedStatusCode()
    {
        $past_log_entry = new LogEntry();
        $past_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:12'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($past_log_entry);
        $current_log_entry = new LogEntry();
        $current_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(422);
        $this->entityManager->persist($current_log_entry);
        $future_log_entry = new LogEntry();
        $future_log_entry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(500);
        $this->entityManager->persist($future_log_entry);
        $this->entityManager->flush();
        $data = http_build_query(['statusCode' => 500]);

        $this->client->request('GET', "logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'count' => 1,
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
