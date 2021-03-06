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


class RegisterDriversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('miky.registry.grid_driver')) {
            return;
        }

        $registry = $container->findDefinition('miky.registry.grid_driver');

        foreach ($container->findTaggedServiceIds('miky.grid_driver') as $id => $attributes) {
            if (!isset($attributes[0]['alias']))  {
                throw new \InvalidArgumentException('Tagged grid drivers needs to have `alias` attribute.');
            }

            $registry->addMethodCall('register', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
