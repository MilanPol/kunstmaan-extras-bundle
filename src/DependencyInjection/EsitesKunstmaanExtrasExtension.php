<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DependencyInjection;

use Esites\KunstmaanExtrasBundle\Constants\ConfigConstants;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class EsitesKunstmaanExtrasExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $configuration = new Configuration();
        $config = $this->processConfiguration(
            $configuration,
            $configs
        );

        foreach (ConfigConstants::getConfiguration() as $configuration) {
            $container->setParameter(
                ConfigConstants::getParameterKeyName(
                    $configuration
                ),
                $config[$configuration]
            );
        }

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');
    }
}
