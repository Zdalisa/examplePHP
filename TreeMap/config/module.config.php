<?php
namespace TreeMap;

use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\CreativeTaskTreeMapService::class => Service\Factory\CreativeTaskTreeMapServiceFactory::class,
            Service\MediaCompaniesTargetTreeMapService::class => Service\Factory\MediaCompaniesTargetTreeMapServiceFactory::class,
            Service\TreeMapService::class => InvokableFactory::class,
        ]
    ],
    'view_manager' => [
        'strategies' => ['ViewJsonStrategy']
    ]
];
