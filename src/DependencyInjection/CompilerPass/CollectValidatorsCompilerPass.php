<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestConverterBundle\DependencyInjection\CompilerPass;

use GepurIt\RequestConverterBundle\RequestConverter\RequestConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CollectValidatorsCompilerPass
 * @package GepurIt\RequestConverterBundle\DependencyInjection\CompilerPass
 */
class CollectValidatorsCompilerPass implements CompilerPassInterface
{
    const REGISTRY_ITEM_TAG = 'request.validator';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $validators       = $container->findTaggedServiceIds(self::REGISTRY_ITEM_TAG);
        $requestValidator = $container->getDefinition(RequestConverter::class);

        foreach (array_keys($validators) as $itemName) {
            $item = $container->getDefinition($itemName);
            $requestValidator->addMethodCall('addDTOService', [$itemName, $item]);
        }
    }
}
