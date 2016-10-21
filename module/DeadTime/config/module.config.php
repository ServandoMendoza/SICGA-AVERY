<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'DeadTime\Controller\DeadTime' => 'DeadTime\Controller\DeadTimeController',
            'DeadTime\Controller\DeadCode' => 'DeadTime\Controller\DeadCodeController',
            'DeadTime\Controller\Requisition' => 'DeadTime\Controller\RequisitionController',
            'DeadTime\Controller\Work' => 'DeadTime\Controller\WorkController',
            'DeadTime\Controller\OpenOrder' => 'DeadTime\Controller\OpenOrderController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'DeadTime' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/DeadTime[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'DeadTime\Controller\DeadTime',
                        'action'     => 'index',
                    ),
                ),
            ),
            'DeadCode' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/DeadCode[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'DeadTime\Controller\DeadCode',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Requisition' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Requisition[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'DeadTime\Controller\Requisition',
                        'action'     => 'index',
                    ),
                ),
            ),
            'OpenOrder' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/OpenOrder[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'DeadTime\Controller\OpenOrder',
                        'action'     => 'add',
                    ),
                ),
            ),
            'Work' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Work[/:action][/:id][/:var][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'var' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'val'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'DeadTime\Controller\Work',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'DeadTime' => __DIR__ . '/../view',
        ),
    ),
);
