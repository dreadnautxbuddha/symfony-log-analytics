<?php

namespace Dreadnaut\LogAnalyticsBundle\Controller\LogEntries;

use Dreadnaut\LogAnalyticsBundle\Controller\Support\Contracts\InvokableControllerInterface;
use Dreadnaut\LogAnalyticsBundle\Repository\LogEntryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Deletes all log entries from the database.
 *
 * @package Dreadnaut\LogAnalyticsBundle\Controller\LogEntries
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
#[Route('/logs', name: 'log_entries.delete', methods: ['DELETE'])]
class TruncateController implements InvokableControllerInterface
{
    /**
     * @param LogEntryRepository $repository
     *
     * @return JsonResponse
     */
    public function __invoke(LogEntryRepository $repository): JsonResponse
    {
        $repository->truncate();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
