<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DependencyInjection;

use Esites\KunstmaanExtrasBundle\Constants\ConfigConstants;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(ConfigConstants::PREFIX_KEY);

        $rootNode
            ->children()
            ->scalarNode(ConfigConstants::MAILER_NAME)
                ->defaultNull()
                ->end()
            ->scalarNode(ConfigConstants::MAILER_USER)
                ->defaultNull()
                ->end()
        ;

        return $treeBuilder;
    }
}
