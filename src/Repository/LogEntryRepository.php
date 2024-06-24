<?php

namespace App\Repository;

use App\Entity\LogEntry;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
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
     * @param array       $service_names
     * @param int|null    $status_code
     * @param string|null $start_date
     * @param string|null $end_date
     *
     * @return int
     */
    public function countBy(
        array $service_names = [],
        ?int $status_code = null,
        ?string $start_date = null,
        ?string $end_date = null,
    ): int
    {
        $criteria = new Criteria();

        if (! empty($service_names)) {
            $criteria->where($criteria->expr()->in('service_name', $service_names));
        }
        if ($status_code) {
            $criteria->where($criteria->expr()->eq('http_status_code', $status_code));
        }
        try {
            if ($start_date) {
                $criteria->where($criteria->expr()->gte('logged_at', new DateTimeImmutable($start_date)));
            }
            if ($end_date) {
                $criteria->where($criteria->expr()->lte('logged_at', new DateTimeImmutable($end_date)));
            }
        } catch (Exception) {
            // If it so happens that an exception is thrown while we're creating datetimes using the supplied format,
            // we're just going to ignore it and not add the constraint as part of the query at all.
        }

        return $this->matching($criteria)->count();
    }
}
