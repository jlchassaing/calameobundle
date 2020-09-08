<?php

namespace CalameoBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class CalameoConfiguration
 * @package Acme\ExampleBundle\DependencyInjection
 *
 *
 *    iframe:
url: "https://v.calameo.com/"
Params:
mode: 'mini'
clikto: 'view'
clicktarget: '_blank'
autoflip: 4
showsharemenu: false
toc:
url: "http://d.calameo.com/3.0.0/toc.php"
links:
url: "http://d.calameo.com/3.0.0/links.php"
api:
key: ~
secret: ~
url: "http://api.calameo.com/1.0/"
keys:
authid: "auth_id"
bkcode: "book_id"
 */

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('calameo');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode->children()
                ->arrayNode( 'paths' )
                   ->children()
                       ->scalarNode( 'iframe' )->defaultValue('//v.calameo.com/')->end()
                       ->scalarNode('toc')->defaultValue('http://api.calameo.com/1.0')->end()
                       ->scalarNode('links')->defaultValue('http://d.calameo.com/3.0.0/toc.php')->end()
                   ->end()
                ->end()
                ->arrayNode('api')
                   ->children()
                       ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                       ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                   ->end()
                ->end()
                ->arrayNode( 'iframeParams' )
                    ->children()
                        ->scalarNode( 'mode' )
                            ->validate()
                                ->ifNotInArray(['mini', 'viewer'])
                                ->thenInvalid("Invalid iframe mode %s")
                            ->end()
                            ->defaultValue('mini')
                        ->end()
                        ->scalarNode('view')
                            ->validate()
                                ->ifNotInArray(['', 'book', 'slide', 'scroll'])
                                ->thenInvalid("Invalid view type %s")
                            ->end()
                        ->end()
                        ->scalarNode('clicktarget')->defaultValue('_blank')->end()
                        ->scalarNode('clickto')->defaultValue('view')->end()
                        ->scalarNode("showsharemenu")->defaultValue('false')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
