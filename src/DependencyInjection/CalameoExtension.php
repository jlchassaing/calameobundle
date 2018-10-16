<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 */

namespace CalameoBundle\DependencyInjection;


use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class CalameoExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('fieldtypes.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter("calameo", $config);
    }

    public function prepend(ContainerBuilder $container)
    {
        $configDirectoryPath = __DIR__.'/../Resources/config';

        $this->prependYamlConfigFile($container, 'ez_platformui', $configDirectoryPath.'/yui.yml');
        $this->prependYamlConfigFile($container, 'ezpublish', $configDirectoryPath.'/ez_field_templates.yml');
        $this->prependYamlConfigFile($container, 'calameo', $configDirectoryPath.'/calameo.yml', 'calameo');
    }

    private function prependYamlConfigFile(ContainerBuilder $container, $extensionName, $configFilePath, $param = null)
    {
        $config = Yaml::parse(file_get_contents($configFilePath));
        $container->prependExtensionConfig($extensionName, $param !== null ? $config[$param]: $config);
    }

}
