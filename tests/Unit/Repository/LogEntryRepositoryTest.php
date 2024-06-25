<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Unit\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Dreadnaut\LogAnalyticsBundle\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogEntryRepositoryTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    // phpcs:ignore
    public function testCountBy_whenSuppliedWithInvalidDateTimeString_shouldNotThrowException()
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
        $managerRegistry = $this->getContainer()->get(ManagerRegistry::class);
        $repository = new LogEntryRepository($managerRegistry);

        $count = $repository->countBy([], null, 'invalid date', 'invalid date');

        $this->assertEquals(3, $count);
    }
}
