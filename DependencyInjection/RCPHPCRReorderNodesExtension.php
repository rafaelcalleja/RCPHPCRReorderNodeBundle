<?php

namespace RC\PHPCRReorderNodesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RCPHPCRReorderNodesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $keys = array_keys($config['nodes']);
        
        $nodes = array();
        
        foreach($keys as $nodename){
        	if(isset($config['templates'][$nodename]) && isset($config['roles'][$nodename])){
        		$childrens = (isset($config['childrens'][$nodename]) && $config['childrens'][$nodename]) ? true : false ;
        		$denied_root = (isset($config['denied_root'][$nodename]) && $config['denied_root'][$nodename]) ? true : false ;
       			$nodes[$nodename] = array('denied_root' => $denied_root, 'childrens' => $childrens, 'node' => $config['nodes'][$nodename], 'template' => $config['templates'][$nodename], 'role' => $config['roles'][$nodename]);
        	}
        }
        
        var_dump($nodes);
        
        $container->setParameter('rcphpcr_reorder_nodes.nodes', $nodes);
        
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
    
    
}
