<?php

namespace App\Controller\LogEntries;

use App\Controller\Support\Contracts\InvokableControllerInterface;
use App\Entity\LogEntry;
use App\Repository\LogEntryRepository;
use App\Request\LogEntries\CountRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Returns the number of log entries that matches the constraints supplied in the request body.
 *
 * The request body looks like this:
 * <code>
 *     {
 *         "serviceNames": [
 *              "INVOICE-SERVICE",
 *              "USER-SERVICE",
 *         ],
 *         "startDate": "2019/07/28 21:15:11 +000",
 *         "endDate": "2019/07/28 21:15:11 +000",
 *         "statusCode": 200,
 *     }
 * </code>
 *
 * with the response looking like this:
 *
 * <code>
 *     {
 *         "count": 1150
 *     }
 * </code>
 *
 * @package App\Controller\LogEntries
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
#[Route('/logs/count', name: 'log_entries.count', methods: ['GET'])]
class CountController implements InvokableControllerInterface
{
    protected LogEntryRepository $repository;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(LogEntry::class);
    }

    /**
     * @todo Move SQL query to repository for reusability
     *
     * @param ValidatorInterface $validator
     * @param CountRequest       $request
     *
     * @return JsonResponse
     */
    public function __invoke(
        ValidatorInterface $validator,
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        CountRequest $request = new CountRequest()
    ): JsonResponse
    {
        $query = $this->repository->createQueryBuilder('log_entry')->select('count(log_entry.id) as count');

        if ($request->statusCode) {
            $query
                ->where('log_entry.http_status_code = :status_code')
                ->setParameter('status_code', $request->statusCode);
        }
        if ($request->startDate) {
            $query->where('log_entry.logged_at >= :start_date')->setParameter('start_date', $request->startDate);
        }
        if ($request->endDate) {
            $query->where('log_entry.logged_at <= :end_date')->setParameter('end_date', $request->endDate);
        }

        [['count' => $count]] = $query->getQuery()->execute();

        return new JsonResponse(['data' => ['count' => $count]]);
    }
}
