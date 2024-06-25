<?php

namespace Dreadnaut\LogAnalyticsBundle\Controller\LogEntries;

use Dreadnaut\LogAnalyticsBundle\Controller\Support\Contracts\InvokableControllerInterface;
use Dreadnaut\LogAnalyticsBundle\Repository\LogEntryRepository;
use Dreadnaut\LogAnalyticsBundle\Request\LogEntries\CountRequest;
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
 *         "data": {
 *             "count": 1150
 *         }
 *     }
 * </code>
 *
 * @package Dreadnaut\LogAnalyticsBundle\Controller\LogEntries
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
#[Route('/logs/count', name: 'log_entries.count', methods: ['GET'])]
class CountController implements InvokableControllerInterface
{
    /**
     * @param ValidatorInterface $validator
     * @param LogEntryRepository $repository
     *
     * @param CountRequest       $request
     *
     * @return JsonResponse
     */
    public function __invoke(
        ValidatorInterface $validator,
        LogEntryRepository $repository,
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        CountRequest $request = new CountRequest(),
    ): JsonResponse
    {
        $count = $repository->countBy(
            $request->serviceNames,
            $request->statusCode,
            $request->startDate,
            $request->endDate
        );

        return new JsonResponse(['data' => ['count' => $count]]);
    }
}
