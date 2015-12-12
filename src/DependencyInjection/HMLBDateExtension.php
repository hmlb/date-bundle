<?php

namespace HMLB\DateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * HMLBDateExtension.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class HMLBDateExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        return $config;
    }

    public function getAlias()
    {
        return 'hmlb_date';
    }
}
