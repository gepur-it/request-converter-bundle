<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * Class RequestValidationException
 * @package GepurIt\RequestConverterBundle\Exception
 */
class RequestValidationException extends \RuntimeException implements HttpExceptionInterface
{
    /** @var ConstraintViolationListInterface */
    private $violationList;


    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface $violationList
     * @param string                           $message
     * @param int                              $code
     * @param Throwable|null                   $previous
     */
    public function __construct(
        ConstraintViolationListInterface $violationList,
        string $message = "",
        int $code = 422,
        Throwable $previous = null
    ) {
        $this->violationList = $violationList;
        $this->code = $code;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return [];
    }
}
