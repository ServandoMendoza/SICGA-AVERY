<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Scrap\Controller\Scrap' => 'Scrap\Controller\ScrapController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'Scrap' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Scrap[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Scrap\Controller\Scrap',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Scrap' => __DIR__ . '/../view',
        ),
    ),
);
