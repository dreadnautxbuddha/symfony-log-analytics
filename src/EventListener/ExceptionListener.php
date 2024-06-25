<?php

namespace Dreadnaut\LogAnalyticsBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Primarily used to format returned exceptions based on the kind.
 *
 * @package Dreadnaut\LogAnalyticsBundle\EventListener
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class ExceptionListener
{
    /**
     * @todo The formatters here may benefit from being moved to different classes or methods. Do it?
     *
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof UnprocessableEntityHttpException) {
            /** @var ValidationFailedException $validationFailedException */
            $validationFailedException = $exception->getPrevious();

            $formattedErrors = [];
            foreach ($validationFailedException->getViolations() as $error) {
                $formattedErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            $response = new JsonResponse(['errors' => $formattedErrors], $exception->getStatusCode());

            $event->setResponse($response);
        }
    }
}
