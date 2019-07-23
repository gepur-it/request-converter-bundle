<?php
/**
 * request-converter-bundle.
 * Author: Andrii Yakovlev <yawa20@gmail.com>
 * Date: 23.07.19
 */
declare(strict_types=1);

namespace GepurIt\RequestConverterBundle\Contract;

/**
 * Interface RequestModelInterface
 * @package GepurIt\RequestConverterBundle\Contract
 */
interface RequestModelServiceInterface
{
    public function handle();
}