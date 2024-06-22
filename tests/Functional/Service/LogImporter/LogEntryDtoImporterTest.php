<?php

namespace tests\Functional\Service\LogImporter;

use App\Dto\Entity\LogEntry\Assembler\FromString;
use App\Entity\LogEntry;
use App\Enum\Http\RequestMethod;
use App\Service\LogImporter\LogEntryDtoImporter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogEntryDtoImporterTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected LogEntryDtoImporter $logImporter;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->logImporter = new LogEntryDtoImporter($this->entityManager);
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
        $this->assertEquals('1.1', $log_entry->getHttpProtocolVersion());
        $this->assertEquals('400', $log_entry->getHttpStatusCode());
    }
}
