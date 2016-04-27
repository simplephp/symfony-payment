<?php

namespace simplephp\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('payment');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('alipay')
                    ->children()
                        ->scalarNode('partner')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('cacert')->defaultValue("%kernel.root_dir%/../vendor/simplephp/Bundle/src/Alipay/cacert.pem")->end()
                ->end()
            ->end()
            ->arrayNode('payease')
                ->children()
                    ->scalarNode('security_code')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('mid')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('paypal')
                ->children()
                    ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
