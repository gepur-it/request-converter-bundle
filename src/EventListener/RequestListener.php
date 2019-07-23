<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use GepurIt\RequestConverterBundle\Annotations\RequestDTO;
use GepurIt\RequestConverterBundle\Exception\RequestModelNotFoundException;
use GepurIt\RequestConverterBundle\Exception\RequestValidationException;
use GepurIt\RequestConverterBundle\RequestConverter\RequestConverter;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestListener
 * @package GepurIt\RequestConverterBundle\EventListener
 */
class RequestListener
{
    /** @var RequestConverter */
    private $requestConverter;

    /** @var Reader */
    private $annotationReader;

    /** @var ValidatorInterface */
    private $validator;

    /**
     * RequestListener constructor.
     *
     * @param RequestConverter   $requestConverter
     * @param Reader             $annotationReader
     * @param ValidatorInterface $validator
     */
    public function __construct(
        RequestConverter $requestConverter,
        Reader $annotationReader,
        ValidatorInterface $validator
    ) {
        $this->requestConverter = $requestConverter;
        $this->annotationReader = $annotationReader;
        $this->validator = $validator;
    }

    /**
     * @param ControllerEvent $event
     * @throws \ReflectionException
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        list($controllerObject, $methodName) = $controller;

        $controllerReflection = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflection->getMethod($methodName);
        /** @var RequestDTO|null $methodAnnotation */
        $methodAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RequestDTO::class);
        if (null === $methodAnnotation || empty($dtoName = $methodAnnotation->model)) {
            return;
        }

        $request    = $event->getRequest();

        $requestDTO = null;
        if ($this->requestConverter->exists($dtoName)) {
            $requestDTO = $this->requestConverter->get($dtoName);
        } elseif (class_exists($dtoName)) {
            $requestDTO = new $dtoName(...$methodAnnotation->arguments);
        }
        if (null === $requestDTO) {
            throw new RequestModelNotFoundException("requestDTO {$dtoName} not found");
        }

        $this->requestConverter->buildRequestModel($request, $requestDTO);
        if ($methodAnnotation->validate) {
            $violations = $this->validator->validate($requestDTO);
            if ($violations->count() > 0) {
                throw new RequestValidationException($violations);
            }
        }

        $request->attributes->set($methodAnnotation->name, $requestDTO);
    }
}
