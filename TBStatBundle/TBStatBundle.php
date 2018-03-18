<?php

namespace TBStatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;

class TBStatBundle extends Bundle
{
    
    private $containerBuilder = null;
    
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
        parent::build($container);
        // Similar to $loader = $kernel->getContainerLoader($this->container);
        $pathToMe  = $this->getPath();
        
        $container->setParameter('things.board.stat.bundle.dir', $pathToMe);
        
        $locator = new FileLocator($pathToMe);
        $resolver = new LoaderResolver(array(
            new YamlFileLoader($container, $locator),
        ));

        $loader = new DelegatingLoader($resolver);
        $loader->load($pathToMe.'/Resources/config/config.yml');
        
    }
    
    public function boot() {
        parent::boot();
        
        $kernel= $this->container->get('kernel');
        
    }
}
