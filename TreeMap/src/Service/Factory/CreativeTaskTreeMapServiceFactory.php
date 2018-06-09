<?php
namespace TreeMap\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use TreeMap\Service\CreativeTaskTreeMapService;

class CreativeTaskTreeMapServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $entityManager = $container->get(\Doctrine\ORM\EntityManager::class);

        return new CreativeTaskTreeMapService(
            $entityManager
        );
    }
}
