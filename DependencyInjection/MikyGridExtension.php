<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\GridBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class MikyGridExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        foreach (['filter', 'action'] as $templatesCollectionName) {
            $templates = isset($config['templates'][$templatesCollectionName]) ? $config['templates'][$templatesCollectionName] : [];
            $container->setParameter('miky.grid.templates.'.$templatesCollectionName, $templates);
        }

        $container->setParameter('miky.grids_definitions', $config['grids']);

        $container->setAlias('miky.grid.renderer', 'miky.grid.renderer.twig');
        $container->setAlias('miky.grid.data_extractor', 'miky.grid.data_extractor.property_access');

        foreach ($config['drivers'] as $enabledDriver) {
            $path = sprintf('driver/%s.xml', $enabledDriver);
            $loader->load($path);
        }
    }
}
