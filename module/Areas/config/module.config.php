<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Areas\Controller\Areas' => 'Areas\Controller\AreasController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'Areas' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Areas[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Areas\Controller\Areas',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Areas' => __DIR__ . '/../view',
        ),
    ),
);
