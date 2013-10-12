<?php

namespace Trt\AsyncTasksBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TrtAsyncTasksExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias().'.mq.client_class' ,$config['mq']['client_class']);
        $container->setParameter($this->getAlias().'.mq.connection_params' ,$config['mq']['connection_params']);
        $container->setParameter($this->getAlias().'.mq.exchange' ,$config['mq']['exchange']);
        $container->setParameter($this->getAlias().'.mq.host' ,$config['mq']['host']);
        $container->setParameter($this->getAlias().'.mq.port' ,$config['mq']['port']);
        $container->setParameter($this->getAlias().'.event.prefix' ,$config['event']['prefix']);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
