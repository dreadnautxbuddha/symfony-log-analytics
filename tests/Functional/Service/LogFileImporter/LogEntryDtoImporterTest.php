<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Functional\Service\LogFileImporter;

use Dreadnaut\LogAnalyticsBundle\Dto\Entity\LogEntry\Assembler\FromString;
use Dreadnaut\LogAnalyticsBundle\Entity\Assembler\LogEntry\FromLogEntryDto;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter\LogEntryDtoImporter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Dreadnaut\LogAnalyticsBundle\Entity\Assembler\Support\Contracts\EntityAssemblerInterface;

class LogEntryDtoImporterTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected LogEntryDtoImporter $logImporter;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->logImporter = new LogEntryDtoImporter($this->entityManager, new FromLogEntryDto());
    }

    public function testImport_whenNotEmpty_shouldSaveToDatabase()
    {
        $valid_log_entry = new FromString('USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400');
        $repository = $this->entityManager->getRepository(LogEntry::class);

        $this->logImporter->import([
            $valid_log_entry->assemble()
        ]);
        $log_entries = $repository->findAll();
        /** @var LogEntry $log_entry */
        [$log_entry] = $log_entries;

        $this->assertCount(1, $log_entries);
        $this->assertEquals('USER-SERVICE', $log_entry->getServiceName());
        $this->assertEquals(new DateTimeImmutable('17/Aug/2018:09:21:54 +0000'), $log_entry->getLoggedAt());
        $this->assertEquals(RequestMethod::POST, $log_entry->getHttpRequestMethod());
        $this->assertEquals('/users', $log_entry->getHttpRequestTarget());
        $this->assertEquals('1.1', $log_entry->getHttpVersion());
        $this->assertEquals('400', $log_entry->getHttpStatusCode());
    }

    public function testImport_whenLogEntryCannotBeCreatedFromSuppliedDto_shouldNotImport()
    {
        /** @var EntityAssemblerInterface $assembler */
        $assembler = $this->createMock(FromLogEntryDto::class);
        $assembler->expects($this->once())->method('assemble')->willReturn(null);
        $log_importer = new LogEntryDtoImporter($this->entityManager, $assembler);
        $valid_log_entry = new FromString('USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400');
        $repository = $this->entityManager->getRepository(LogEntry::class);

        $log_importer->import([$valid_log_entry->assemble()]);
        $log_entries = $repository->findAll();

        $this->assertEmpty($log_entries);
    }
}
