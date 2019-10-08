<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\RequestConverter;

use GepurIt\RequestConverterBundle\Contract\RequestModelServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class RequestValidator
 * @package GepurIt\RequestConverterBundle\RequestValidator
 */
class RequestConverter
{
    /** @var RequestModelServiceInterface[] */
    private $dtoServices = [];

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /**
     * RequestConverter constructor.
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param string                       $name
     * @param RequestModelServiceInterface $requestDTO
     */
    public function addDTOService(string $name, RequestModelServiceInterface $requestDTO)
    {
        $this->dtoServices[$name] = $requestDTO;
    }

    /**
     * @param Request $request
     * @param object  $requestDTO
     *
     * @return object
     */
    public function buildRequestModel(Request $request, object $requestDTO)
    {
        foreach ($request->request->all() as $name => $value) {
            if ($this->propertyAccessor->isWritable($requestDTO, $name)) {
                $this->propertyAccessor->setValue($requestDTO, $name, $value);
            }
        }
        foreach ($request->attributes->all() as $name => $value) {
            if ($this->propertyAccessor->isWritable($requestDTO, $name)) {
                $this->propertyAccessor->setValue($requestDTO, $name, $value);
            }
        }
        foreach ($request->query->all() as $name => $value) {
            if ($this->propertyAccessor->isWritable($requestDTO, $name)) {
                $this->propertyAccessor->setValue($requestDTO, $name, $value);
            }
        }
        if ($requestDTO instanceof RequestModelServiceInterface) {
            $requestDTO->handle();
        }
        return $requestDTO;
    }

    /**
     * @param $dtoServiceName
     *
     * @return bool
     */
    public function exists($dtoServiceName)
    {
        return array_key_exists($dtoServiceName, $this->dtoServices);
    }

    /**
     * @param $dtoServiceName
     *
     * @return RequestModelServiceInterface
     */
    public function get(string $dtoServiceName): RequestModelServiceInterface
    {
        return $this->dtoServices[$dtoServiceName];
    }
}
