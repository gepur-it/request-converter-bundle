<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\Annotations;

/**
 * @Annotation
 *
 * Class RequestValidation
 * @package GepurIt\RequestConverterBundle\Annotations
 */
class RequestDTO
{
    /** @var string $model */
    public $model = '';

    /** @var array  */
    public $arguments = [];

    /** @var bool */
    public $validate = true;

    /** @var string */
    public $name = 'requestDTO';
}
