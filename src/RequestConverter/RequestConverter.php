<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\RequestConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class RequestValidator
 * @package GepurIt\RequestConverterBundle\RequestValidator
 */
class RequestConverter
{
    /** @var object[] */
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
     * @param string  $name
     * @param object  $requestDTO
     */
    public function addDTOService(string $name, object $requestDTO)
    {
        $this->dtoServices[$name] = $requestDTO;
    }

    /**
     * @param Request $request
     * @param object  $requestDTO
     *
     * @return object
     */
    public function buildRequestDTO(Request $request, object $requestDTO)
    {
        foreach ($request->request->all() as $name => $value) {
            if ($this->propertyAccessor->isWritable($requestDTO, $name)) {
                $this->propertyAccessor->setValue($requestDTO, $name, $value);
            }
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
     * @param $validatorName
     *
     * @return object
     */
    public function get($dtoServiceName)
    {
        return $this->dtoServices[$dtoServiceName];
    }
}
