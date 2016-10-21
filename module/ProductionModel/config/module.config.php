<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'ProductionModel\Controller\ProductionModel' => 'ProductionModel\Controller\ProductionModelController',
            'ProductionModel\Controller\StandardProduction' => 'ProductionModel\Controller\StandardProductionController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'ProductionModel' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ProductionModel[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ProductionModel\Controller\ProductionModel',
                        'action'     => 'index',
                    ),
                ),
            ),
            'StandardProduction' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/StandardProduction[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ProductionModel\Controller\StandardProduction',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ProductionModel' => __DIR__ . '/../view',
        ),
    ),
);

