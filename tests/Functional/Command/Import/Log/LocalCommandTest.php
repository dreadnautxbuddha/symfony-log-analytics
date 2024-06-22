<?php

namespace Tests\Functional\Command\Import\Log;

use App\Entity\LogEntry;
use App\Enum\Http\RequestMethod;
use App\Repository\LogEntryRepository;
use App\Service\LogFileImporter\LogEntryDtoImporter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class LocalCommandTest extends KernelTestCase
{
    protected Command $command;
    protected EntityManagerInterface $entityManager;
    protected LogEntryRepository $repository;
    protected array $logEntriesInLogFile = [
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:21:53 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:21:54 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 400,
        ],
        [
            'service_name' => 'INVOICE-SERVICE',
            'logged_at' => '17/Aug/2018:09:21:55 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/invoices',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:21:56 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:21:57 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'INVOICE-SERVICE',
            'logged_at' => '17/Aug/2018:09:22:58 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/invoices',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'INVOICE-SERVICE',
            'logged_at' => '17/Aug/2018:09:22:59 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/invoices',
            'http_version' => '1.1',
            'http_status_code' => 400,
        ],
        [
            'service_name' => 'INVOICE-SERVICE',
            'logged_at' => '17/Aug/2018:09:23:53 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/invoices',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:23:54 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 400,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:23:55 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:26:51 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'INVOICE-SERVICE',
            'logged_at' => '17/Aug/2018:09:26:53 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/invoices',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:29:11 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '17/Aug/2018:09:29:13 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '18/Aug/2018:09:30:54 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 400,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '18/Aug/2018:09:31:55 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '18/Aug/2018:09:31:56 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'INVOICE-SERVICE',
            'logged_at' => '18/Aug/2018:10:26:53 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/invoices',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '18/Aug/2018:10:32:56 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
        [
            'service_name' => 'USER-SERVICE',
            'logged_at' => '18/Aug/2018:10:33:59 +0000',
            'http_request_method' => 'POST',
            'http_request_target' => '/users',
            'http_version' => '1.1',
            'http_status_code' => 201,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $application = new Application(self::$kernel);
        $this->command = $application->find('import:log:local');
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(LogEntry::class);
    }

    public function testExecute_whenFileAtSpecifiedPathDoesNotExist_shouldReturnError()
    {
        $command_tester = new CommandTester($this->command);

        $exitCode = $command_tester->execute([
            'path' => 'unknown/path/to/file',
        ]);

        $this->assertEquals(1, $exitCode);
    }

    /**
     * @dataProvider invalidInteger
     */
    public function testExecute_whenOffsetIsInvalid_shouldReturnError(mixed $invalidInteger)
    {
        $command_tester = new CommandTester($this->command);

        $exitCode = $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            'offset' => $invalidInteger,
        ]);

        $this->assertEquals(1, $exitCode);
    }

    /**
     * @dataProvider invalidInteger
     */
    public function testExecute_whenLimitIsInvalid_shouldReturnError(mixed $invalidInteger)
    {
        $command_tester = new CommandTester($this->command);

        $exitCode = $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            'limit' => $invalidInteger,
        ]);

        $this->assertEquals(1, $exitCode);
    }

    /**
     * @dataProvider invalidInteger
     */
    public function testExecute_whenChunkSizeIsInvalid_shouldReturnError(mixed $invalidInteger)
    {
        $command_tester = new CommandTester($this->command);

        $exitCode = $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            'chunkSize' => $invalidInteger,
        ]);

        $this->assertEquals(1, $exitCode);
    }

    public function testExecute_whenFileAtSpecifiedPathExists_shouldReturnSuccess()
    {
        $command_tester = new CommandTester($this->command);

        $exitCode = $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
        ]);

        $this->assertEquals(0, $exitCode);
    }

    public function testExecute_whenFileAtSpecifiedPathExists_shouldInsertLogEntriesToTheDatabase()
    {
        $command_tester = new CommandTester($this->command);

        $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
        ]);
        $log_entries = $this->repository->findAll();

        $this->assertCount(20, $log_entries);
        foreach ($log_entries as $index => $log_entry) {
            $this->assertEquals($this->logEntriesInLogFile[$index]['service_name'], $log_entry->getServiceName());
            $this->assertEquals(
                new DateTimeImmutable($this->logEntriesInLogFile[$index]['logged_at']),
                $log_entry->getLoggedAt()
            );
            $this->assertEquals(
                RequestMethod::tryFrom($this->logEntriesInLogFile[$index]['http_request_method']),
                $log_entry->getHttpRequestMethod()
            );
            $this->assertEquals(
                $this->logEntriesInLogFile[$index]['http_request_target'],
                $log_entry->getHttpRequestTarget()
            );
            $this->assertEquals(
                $this->logEntriesInLogFile[$index]['http_version'],
                $log_entry->getHttpProtocolVersion()
            );
            $this->assertEquals(
                $this->logEntriesInLogFile[$index]['http_status_code'],
                $log_entry->getHttpStatusCode()
            );
        }
    }

    public function testExecute_whenChoosingOnlySpecificLinesToImport_shouldOnlyImportThoseLines()
    {
        $command_tester = new CommandTester($this->command);

        $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            'offset' => 5,
            'limit' => 1,
        ]);
        $log_entries = $this->repository->findAll();
        /** @var LogEntry $log_entry */
        [$log_entry] = $log_entries;

        $this->assertCount(1, $log_entries);
        $this->assertEquals('INVOICE-SERVICE', $log_entry->getServiceName());
        $this->assertEquals(new DateTimeImmutable('17/Aug/2018:09:22:58 +0000'), $log_entry->getLoggedAt());
        $this->assertEquals(RequestMethod::from('POST'), $log_entry->getHttpRequestMethod());
        $this->assertEquals('/invoices', $log_entry->getHttpRequestTarget());
        $this->assertEquals('1.1', $log_entry->getHttpProtocolVersion());
        $this->assertEquals(201, $log_entry->getHttpStatusCode());
    }

    public function testExecute_whenChunkSizeIsSupplied_shouldFlushNTimes()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(10))->method('flush');

        $logEntryDtoImporter = new LogEntryDtoImporter($entityManager);
        $this->getContainer()->set(LogEntryDtoImporter::class, $logEntryDtoImporter);
        $command_tester = new CommandTester($this->command);

        $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            'chunkSize' => 2,
        ]);
    }

    public function testExecute_whenChunkSizeIsSupplied_shouldSaveAllLogEntriesToDatabase()
    {
        $command_tester = new CommandTester($this->command);

        $command_tester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            'chunkSize' => 2,
        ]);
        $log_entries = $this->repository->findAll();

        $this->assertCount(20, $log_entries);
    }

    public static function invalidInteger(): array
    {
        return [
            ['a string'],
            [0.1],
        ];
    }
}
