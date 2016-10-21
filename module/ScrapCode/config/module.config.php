<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'ScrapCode\Controller\ScrapCode' => 'ScrapCode\Controller\ScrapCodeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'ScrapCode' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ScrapCode[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ScrapCode\Controller\ScrapCode',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ScrapCode' => __DIR__ . '/../view',
        ),
    ),
);
