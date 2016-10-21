<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Products\Controller\Products' => 'Products\Controller\ProductsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'Products' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Products[/:action][/:sku][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'sku'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Products\Controller\Products',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Products' => __DIR__ . '/../view',
        ),
    ),
);
