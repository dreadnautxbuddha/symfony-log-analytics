<?php

namespace Dreadnaut\LogAnalyticsBundle\Repository;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Dreadnaut\LogAnalyticsBundle\Entity\LogEntry\LogEntry;
use Exception;

/**
 * @extends ServiceEntityRepository<LogEntry>
 */
class LogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntry::class);
    }

    /**
     * Deletes all log entries from the database.
     *
     * @return void
     */
    public function truncate(): void
    {
        $this->createQueryBuilder('logEntry')->delete()->getQuery()->execute();
    }

    /**
     * Returns the total number of log entries whose {@see LogEntry::$service_name} and
     * {@see LogEntry::$http_status_code} matches the supplied one, whose {@see LogEntry::$logged_at} is greater than or
     * equal to the start date, less than or equal to the end date, or in between.
     *
     * @param array<string> $serviceNames
     * @param int|null      $statusCode
     * @param string|null   $startDate
     * @param string|null   $endDate
     *
     * @return int
     */
    public function countBy(
        array $serviceNames = [],
        ?int $statusCode = null,
        ?string $startDate = null,
        ?string $endDate = null,
    ): int {
        $criteria = new Criteria();

        if (! empty($serviceNames)) {
            $criteria->andWhere($criteria->expr()->in('service_name', $serviceNames));
        }
        if ($statusCode) {
            $criteria->andWhere($criteria->expr()->eq('http_status_code', $statusCode));
        }
        try {
            if ($startDate) {
                $criteria->andWhere($criteria->expr()->gte('logged_at', new DateTimeImmutable($startDate)));
            }
            if ($endDate) {
                $criteria->andWhere($criteria->expr()->lte('logged_at', new DateTimeImmutable($endDate)));
            }
        } catch (Exception) {
            // If it so happens that an exception is thrown while we're creating datetimes using the supplied format,
            // we're just going to ignore it and not add the constraint as part of the query at all.
        }

        return $this->matching($criteria)->count();
    }
}
