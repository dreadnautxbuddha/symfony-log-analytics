<?php

namespace App\Controller\LogEntries;

use App\Controller\Support\Contracts\InvokableControllerInterface;
use App\Entity\LogEntry;
use App\Repository\LogEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Deletes all log entries from the database.
 *
 * @package App\Controller\LogEntries
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
#[Route('/logs', name: 'log_entries.delete', methods: ['DELETE'])]
class TruncateController implements InvokableControllerInterface
{
    protected LogEntryRepository $repository;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(LogEntry::class);
    }

    /**
     * @todo Move SQL query to repository for reusability
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $this->repository->createQueryBuilder('log_entry')->delete()->getQuery()->execute();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
