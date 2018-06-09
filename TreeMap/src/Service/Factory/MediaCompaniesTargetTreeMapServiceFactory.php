<?php
namespace TreeMap\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use TreeMap\Service\MediaCompaniesTargetTreeMapService;

class MediaCompaniesTargetTreeMapServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $entityManager = $container->get(\Doctrine\ORM\EntityManager::class);

        return new MediaCompaniesTargetTreeMapService(
            $entityManager
        );
    }
}
