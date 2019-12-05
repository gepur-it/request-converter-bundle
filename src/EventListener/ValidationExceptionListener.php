<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\EventListener;

use GepurIt\RequestConverterBundle\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Class ValidationExceptionListener
 * @package GepurIt\RequestConverterBundle\EventListener
 */
class ValidationExceptionListener
{

    /**
     * ValidationExceptionListener constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param ExceptionEvent $event
     * @throws \ReflectionException
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!($exception instanceof RequestValidationException)) {
            return;
        }
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($exception->getViolationList() as $violation) {
            if(!array_key_exists($violation->getPropertyPath(), $errors)) {
                $errors[$violation->getPropertyPath()] = [];
            }
            $errors[$violation->getPropertyPath()][] = [
                "code" => $violation->getCode(),
                "rule" =>  (new \ReflectionClass($violation->getConstraint()))->getShortName(),
                "message" => $violation->getMessage(),
            ];
        }
        $message = json_encode(["fields"=>$errors]);
        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);
        $response->setStatusCode($exception->getStatusCode(), 'Validation Error');

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
