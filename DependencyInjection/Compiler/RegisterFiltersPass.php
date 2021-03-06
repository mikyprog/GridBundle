<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class RegisterFiltersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('miky.registry.grid_filter')) {
            return;
        }

        $registry = $container->getDefinition('miky.registry.grid_filter');

        foreach ($container->findTaggedServiceIds('miky.grid_filter') as $id => $attributes) {
            if (!isset($attributes[0]['type']))  {
                throw new \InvalidArgumentException('Tagged grid filters needs to have `type` attribute.');
            }

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }
    }
}
