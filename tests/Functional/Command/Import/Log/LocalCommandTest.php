<?php

namespace Dreadnaut\LogAnalyticsBundle\Tests\Functional\Command\Import\Log;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\Assembler\FromLogEntryDto;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry;
use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Dreadnaut\LogAnalyticsBundle\Repository\LogEntryRepository;
use Dreadnaut\LogAnalyticsBundle\Service\LogFileImporter\LogEntryDtoImporter;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

use function trim;

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

    // phpcs:ignore
    public function testExecute_whenFileAtSpecifiedPathDoesNotExist_shouldReturnError()
    {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            'path' => 'unknown/path/to/file',
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertEquals('The file at the specified path could not be found.', trim($commandTester->getDisplay()));
    }

    /**
     * @dataProvider invalidInteger
     */
    // phpcs:ignore
    public function testExecute_whenOffsetIsInvalid_shouldReturnError(
        mixed $invalidInteger,
        mixed $errorMessageIdentifier
    ) {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--offset' => $invalidInteger,
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertEquals(
            "The offset {$errorMessageIdentifier} must be an integer.",
            trim($commandTester->getDisplay())
        );
    }

    /**
     * @dataProvider invalidInteger
     */
    // phpcs:ignore
    public function testExecute_whenLimitIsInvalid_shouldReturnError(
        mixed $invalidInteger,
        mixed $errorMessageIdentifier
    ) {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--limit' => $invalidInteger,
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertEquals(
            "The limit {$errorMessageIdentifier} must be an integer.",
            trim($commandTester->getDisplay())
        );
    }

    /**
     * @dataProvider invalidInteger
     */
    // phpcs:ignore
    public function testExecute_whenChunkSizeIsInvalid_shouldReturnError(
        mixed $invalidInteger,
        mixed $errorMessageIdentifier
    ) {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--chunk-size' => $invalidInteger,
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertEquals(
            "The chunk size {$errorMessageIdentifier} must be an integer.",
            trim($commandTester->getDisplay())
        );
    }

    // phpcs:ignore
    public function testExecute_whenFileAtSpecifiedPathExists_shouldReturnSuccess()
    {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
        ]);

        $this->assertEquals(0, $exitCode);
    }

    // phpcs:ignore
    public function testExecute_whenFileAtSpecifiedPathExists_shouldInsertLogEntriesToTheDatabase()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(['path' => 'tests/Data/Service/LogFileImporter/logs.log']);
        $logEntries = $this->repository->findAll();

        $this->assertCount(20, $logEntries);
        foreach ($logEntries as $index => $logEntry) {
            $this->assertEquals($this->logEntriesInLogFile[$index]['service_name'], $logEntry->getServiceName());
            $this->assertEquals(
                new DateTimeImmutable($this->logEntriesInLogFile[$index]['logged_at']),
                $logEntry->getLoggedAt()
            );
            $this->assertEquals(
                RequestMethod::tryFrom($this->logEntriesInLogFile[$index]['http_request_method']),
                $logEntry->getHttpRequestMethod()
            );
            $this->assertEquals(
                $this->logEntriesInLogFile[$index]['http_request_target'],
                $logEntry->getHttpRequestTarget()
            );
            $this->assertEquals(
                $this->logEntriesInLogFile[$index]['http_version'],
                $logEntry->getHttpVersion()
            );
            $this->assertEquals(
                $this->logEntriesInLogFile[$index]['http_status_code'],
                $logEntry->getHttpStatusCode()
            );
        }
    }

    // phpcs:ignore
    public function testExecute_whenChoosingOnlySpecificLinesToImport_shouldOnlyImportThoseLines()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--offset' => 5,
            '--limit' => 1,
        ]);
        $logEntries = $this->repository->findAll();
        /** @var LogEntry $logEntry */
        [$logEntry] = $logEntries;

        $this->assertCount(1, $logEntries);
        $this->assertEquals('INVOICE-SERVICE', $logEntry->getServiceName());
        $this->assertEquals(new DateTimeImmutable('17/Aug/2018:09:22:58 +0000'), $logEntry->getLoggedAt());
        $this->assertEquals(RequestMethod::from('POST'), $logEntry->getHttpRequestMethod());
        $this->assertEquals('/invoices', $logEntry->getHttpRequestTarget());
        $this->assertEquals('1.1', $logEntry->getHttpVersion());
        $this->assertEquals(201, $logEntry->getHttpStatusCode());
    }

    // phpcs:ignore
    public function testExecute_whenChunkSizeIsSupplied_shouldFlushNTimes()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(10))->method('flush');

        $logEntryDtoImporter = new LogEntryDtoImporter($entityManager, new FromLogEntryDto());
        $this->getContainer()->set(LogEntryDtoImporter::class, $logEntryDtoImporter);
        $commandTester = new CommandTester($this->command);

        $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--chunk-size' => 2,
        ]);
    }

    // phpcs:ignore
    public function testExecute_whenChunkSizeIsSupplied_shouldClearNTimes()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(10))->method('clear');

        $logEntryDtoImporter = new LogEntryDtoImporter($entityManager, new FromLogEntryDto());
        $this->getContainer()->set(LogEntryDtoImporter::class, $logEntryDtoImporter);
        $commandTester = new CommandTester($this->command);

        $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--chunk-size' => 2,
        ]);
    }

    // phpcs:ignore
    public function testExecute_whenChunkSizeIsSupplied_shouldSaveAllLogEntriesToDatabase()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute([
            'path' => 'tests/Data/Service/LogFileImporter/logs.log',
            '--chunk-size' => 2,
        ]);
        $logEntries = $this->repository->findAll();

        $this->assertCount(20, $logEntries);
    }

    // phpcs:ignore
    public function testExecute_whenLogEntryDoesNotMatchExpectedFormat_shouldBeLogged()
    {
        $monolog = $this->createMock(Logger::class);
        $commandTester = new CommandTester($this->command);
        $monolog
            ->expects($this->exactly(28))
            ->method('warning')
            ->with('Skipping log entry with mismatched format');

        $this->getContainer()->set(LoggerInterface::class, $monolog);

        $commandTester->execute(['path' => 'tests/Data/Service/LogFileImporter/bad.log']);
    }

    // phpcs:ignore
    public function testExecute_whenLogEntryDoesNotMatchExpectedFormat_shouldNotSaveLogEntriesToDatabase()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(['path' => 'tests/Data/Service/LogFileImporter/bad.log']);
        $logEntries = $this->repository->findAll();

        $this->assertEmpty($logEntries);
    }

    // phpcs:ignore
    public function testExecute_whenLogEntryContainsBothGoodAndBadEntries_shouldOnlySaveGoodLogEntriesToDatabase()
    {
        $commandTester = new CommandTester($this->command);

        $commandTester->execute(['path' => 'tests/Data/Service/LogFileImporter/mixed.log']);
        $logEntries = $this->repository->findAll();
        [$first, $second, $third] = $logEntries;

        $this->assertCount(3, $logEntries);
        $this->assertEquals('USER-SERVICE', $first->getServiceName());
        $this->assertEquals(new DateTimeImmutable('17/Aug/2018:09:21:53 +0000'), $first->getLoggedAt());
        $this->assertEquals(RequestMethod::POST, $first->getHttpRequestMethod());
        $this->assertEquals('/users', $first->getHttpRequestTarget());
        $this->assertEquals(1.1, $first->getHttpVersion());
        $this->assertEquals(201, $first->getHttpStatusCode());
        $this->assertEquals('USER-SERVICE', $second->getServiceName());
        $this->assertEquals(new DateTimeImmutable('17/Aug/2018:09:29:13 +0000'), $second->getLoggedAt());
        $this->assertEquals(RequestMethod::POST, $second->getHttpRequestMethod());
        $this->assertEquals('/users', $second->getHttpRequestTarget());
        $this->assertEquals(1.1, $second->getHttpVersion());
        $this->assertEquals(201, $second->getHttpStatusCode());
        $this->assertEquals('USER-SERVICE', $third->getServiceName());
        $this->assertEquals(new DateTimeImmutable('18/Aug/2018:09:30:54 +0000'), $third->getLoggedAt());
        $this->assertEquals(RequestMethod::POST, $third->getHttpRequestMethod());
        $this->assertEquals('/users', $third->getHttpRequestTarget());
        $this->assertEquals(1.1, $third->getHttpVersion());
        $this->assertEquals(400, $third->getHttpStatusCode());
    }

    public static function invalidInteger(): array
    {
        return [
            ['a string', '"a string"'],
            [0.1, '"0.1"'],
        ];
    }
}
