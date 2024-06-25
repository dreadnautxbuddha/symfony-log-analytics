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

    public function testCountBy_whenSuppliedWithInvalidDateTimeString_shouldNotThrowException()
    {
        $past_log_entry = new LogEntry();
        $past_log_entry
            ->setServiceName('my service 1')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:12'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(200);
        $this->entityManager->persist($past_log_entry);
        $current_log_entry = new LogEntry();
        $current_log_entry
            ->setServiceName('my service 2')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:14'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(422);
        $this->entityManager->persist($current_log_entry);
        $future_log_entry = new LogEntry();
        $future_log_entry
            ->setServiceName('my service 3')
            ->setLoggedAt(new DateTimeImmutable('2024-06-30 22:15:15'))
            ->setHttpRequestMethod(RequestMethod::POST)
            ->setHttpRequestTarget('/my/api/endpoint')
            ->setHttpVersion('1.1')
            ->setHttpStatusCode(500);
        $this->entityManager->persist($future_log_entry);
        $this->entityManager->flush();
        $manager_registry = $this->getContainer()->get(ManagerRegistry::class);
        $repository = new LogEntryRepository($manager_registry);

        $count = $repository->countBy([], null, 'invalid date', 'invalid date');

        $this->assertEquals(3, $count);
    }
}
