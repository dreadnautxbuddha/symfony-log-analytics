<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Functional\Service\LogFileImporter;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\Assembler\FromLogEntryDto;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\Entity\Support\Contracts\EntityAssemblerInterface;
use Dreadnaut\LogAnalyticsBundle\EntityDto\LogEntry\Assembler\FromString;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter\LogEntryDtoImporter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogEntryDtoImporterTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected LogEntryDtoImporter $logImporter;
    protected FromString $assembler;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->logImporter = new LogEntryDtoImporter($this->entityManager, new FromLogEntryDto());
        $this->assembler = new FromString();
    }

    // phpcs:ignore
    public function testImport_whenNotEmpty_shouldSaveToDatabase()
    {
        $line = 'USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400';
        $repository = $this->entityManager->getRepository(LogEntry::class);

        $this->logImporter->import([$this->assembler->assemble($line)]);
        $logEntries = $repository->findAll();
        /** @var LogEntry $logEntry */
        [$logEntry] = $logEntries;

        $this->assertCount(1, $logEntries);
        $this->assertEquals('USER-SERVICE', $logEntry->getServiceName());
        $this->assertEquals(new DateTimeImmutable('17/Aug/2018:09:21:54 +0000'), $logEntry->getLoggedAt());
        $this->assertEquals(RequestMethod::POST, $logEntry->getHttpRequestMethod());
        $this->assertEquals('/users', $logEntry->getHttpRequestTarget());
        $this->assertEquals('1.1', $logEntry->getHttpVersion());
        $this->assertEquals('400', $logEntry->getHttpStatusCode());
    }

    // phpcs:ignore
    public function testImport_whenLogEntryCannotBeCreatedFromSuppliedDto_shouldNotImport()
    {
        /** @var EntityAssemblerInterface $assembler */
        $assembler = $this->createMock(FromLogEntryDto::class);
        $assembler->expects($this->once())->method('assemble')->willReturn(null);
        $logImporter = new LogEntryDtoImporter($this->entityManager, $assembler);
        $line = 'USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400';
        $repository = $this->entityManager->getRepository(LogEntry::class);

        $logImporter->import([$this->assembler->assemble($line)]);
        $logEntries = $repository->findAll();

        $this->assertEmpty($logEntries);
    }
}
