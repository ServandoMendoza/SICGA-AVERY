<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Machine\Controller\Machine' => 'Machine\Controller\MachineController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'Machine' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Machine[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Machine\Controller\Machine',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Machine' => __DIR__ . '/../view',
        ),
    ),
);
