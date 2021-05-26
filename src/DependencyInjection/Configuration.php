<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DependencyInjection;

use Esites\KunstmaanExtrasBundle\Constants\ConfigConstants;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(ConfigConstants::PREFIX_KEY);

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $this->getRootNode($treeBuilder);
        $children = $rootNode->children();

        $children
            ->scalarNode(ConfigConstants::MAILER_NAME)
            ->defaultNull()
            ->end()
        ;

        $children
            ->scalarNode(ConfigConstants::MAILER_USER)
            ->defaultNull()
            ->end()
        ;

        $children
            ->booleanNode(ConfigConstants::ENABLE_TRAILING_SLASH_REDIRECT)
            ->defaultFalse()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getRootNode(TreeBuilder $treeBuilder): NodeDefinition
    {
        if (method_exists(
            $treeBuilder,
            'getRootNode'
        )) {
            return $treeBuilder->getRootNode();
        }

        if (method_exists(
            $treeBuilder,
            'root'
        )) {
            // BC layer for symfony/config 4.1 and older
            return $treeBuilder->root(ConfigConstants::PREFIX_KEY);
        }

        throw new InvalidArgumentException('Can not get root node');
    }
}
