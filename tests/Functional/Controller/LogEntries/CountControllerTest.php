<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Functional\Controller\LogEntries;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
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

    // phpcs:ignore
    public function testController_whenAccessed_shouldReturn200(): void
    {
        $this->client->request('GET', '/logs/count');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    // phpcs:ignore
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

    // phpcs:ignore
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
    // phpcs:ignore
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

    // phpcs:ignore
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
                        'This value should be of type ?int.',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    // phpcs:ignore
    public function testController_whenServiceNameIsNotAnArray_shouldReturn422()
    {
        $data = http_build_query(['serviceNames' => 'a string']);

        $this->client->request('GET', "logs/count?{$data}");

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => [
                    'serviceNames' => [
                        'This value should be of type array.',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    // phpcs:ignore
    public function testController_whenQueryingByStartDate_shouldOnlyReturnLogEntriesAfterThatDate()
    {
        $pastLogEntry = new LogEntry();
        $pastLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:13'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($pastLogEntry);
        $currentLogEntry = new LogEntry();
        $currentLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($currentLogEntry);
        $futureLogEntry = new LogEntry();
        $futureLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($futureLogEntry);
        $anotherFutureLogEntry = new LogEntry();
        $anotherFutureLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:16'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($anotherFutureLogEntry);
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

    // phpcs:ignore
    public function testController_whenQueryingByEndDate_shouldOnlyReturnLogEntriesBeforeThatDate()
    {
        $pastLogEntry = new LogEntry();
        $pastLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:12'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($pastLogEntry);
        $currentLogEntry = new LogEntry();
        $currentLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($currentLogEntry);
        $futureLogEntry = new LogEntry();
        $futureLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($futureLogEntry);
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

    // phpcs:ignore
    public function testController_whenQueryingByStatusCode_shouldOnlyReturnLogEntriesWithTheSpecifiedStatusCode()
    {
        $pastLogEntry = new LogEntry();
        $pastLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:12'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($pastLogEntry);
        $currentLogEntry = new LogEntry();
        $currentLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(422);
        $this->entityManager->persist($currentLogEntry);
        $futureLogEntry = new LogEntry();
        $futureLogEntry
            ->setServiceName('my service')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(500);
        $this->entityManager->persist($futureLogEntry);
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

    // phpcs:ignore
    public function testController_whenQueryingByServiceName_shouldOnlyReturnLogEntriesWithTheSpecifiedServiceName()
    {
        $pastLogEntry = new LogEntry();
        $pastLogEntry
            ->setServiceName('my service 1')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:12'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($pastLogEntry);
        $currentLogEntry = new LogEntry();
        $currentLogEntry
            ->setServiceName('my service 2')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(422);
        $this->entityManager->persist($currentLogEntry);
        $futureLogEntry = new LogEntry();
        $futureLogEntry
            ->setServiceName('my service 3')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(500);
        $this->entityManager->persist($futureLogEntry);
        $this->entityManager->flush();
        $data = http_build_query(['serviceNames' => ['my service 1', 'my service 3']]);

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

    public static function invalidHttpStatusCodes(): array
    {
        return [
            [99],
            [600],
        ];
    }
}
