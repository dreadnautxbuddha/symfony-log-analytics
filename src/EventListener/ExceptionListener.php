<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Primarily used to format returned exceptions based on the kind.
 *
 * @package App\EventListener
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class ExceptionListener
{
    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof UnprocessableEntityHttpException) {
            /** @var ValidationFailedException $validation_failed_exception */
            $validation_failed_exception = $exception->getPrevious();

            $formattedErrors = [];
            foreach ($validation_failed_exception->getViolations() as $error) {
                $formattedErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            $response = new JsonResponse(['errors' => $formattedErrors], $exception->getStatusCode());

            $event->setResponse($response);
        }
    }
}
