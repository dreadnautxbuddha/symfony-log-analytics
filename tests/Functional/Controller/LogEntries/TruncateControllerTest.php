<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Functional\Controller\LogEntries;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TruncateControllerTest extends WebTestCase
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
    public function testController_whenDeletingLogs_shouldDeleteAllLogs()
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

        $this->client->request('DELETE', 'logs');
        $logEntries = $this->entityManager->getRepository(LogEntry::class)->findAll();

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty($logEntries);
    }
}
