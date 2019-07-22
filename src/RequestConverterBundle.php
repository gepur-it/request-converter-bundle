<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle;

use GepurIt\RequestConverterBundle\DependencyInjection\CompilerPass\CollectValidatorsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RequestConverterBundle
 * @package GepurIt\RequestConverterBundle
 */
class RequestConverterBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new CollectValidatorsCompilerPass());
    }
}
