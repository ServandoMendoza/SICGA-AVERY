<?php
// http://p0l0.binware.org/index.php/2012/02/18/zend-framework-2-authentication-acl-using-eventmanager/
// First I created an extra config for ACL (could be also in module.config.php, but I prefer to have it in a separated file)
return array(
    'acl' => array(
        'roles' => array(
            'anonimo'   => null,
            'operador'   => null,
            'scrap'  => null,
            'tecnico'  => null,
            'admin'  => null,
            'gerente'  => null,
            'dispatch'  => null,
            'coordinador'  => null,
        ),
        'resources' => array(
            'allow' => array(
                'Application\Controller\Index' => array(
                    'index'	=> array('admin'),
                ),
                'ProductionModel\Controller\StandardProduction' => array(
                    'index'	=> array('admin'),
                    'add'	=> array('admin'),
                    'edit'	=> array('admin'),
                    'isRepeatedAjax'	=> array('admin'),
                    'jsonList'	=> array('admin'),
                    'deleteStandardProductionAjax' => array('admin'),
                ),
                'DeadTime\Controller\OpenOrder' => array(
                    'add'	=> array('admin'),
                ),
                'DeadTime\Controller\DeadCode' => array(
                    'list'	=> array('admin'),
                    'deleteDCAjax'	=> array('admin'),
                    'deleteDeadCodeGroupAjax' => array('admin'),
                    'listGroup'	=> array('admin'),
                    'addGroup'	=> array('operador','tecnico','scrap','admin'),
                    'editGroup'	=> array('operador','tecnico','scrap','admin'),
                    'add'	=> array('admin'),
                    'edit'	=> array('admin'),
                    'jsonListg'	=> array('admin'),
                    'jsonList'	=> array('admin'),
                    'deadCodeExists' => array('admin'),
                    'getDeadCodeGroupByMachine' => array('admin'),
                    'getDeadCodeGroupByName' => array('admin'),
                ),
                'DeadTime\Controller\DeadTime' => array(
                    'index'	=> array('operador','tecnico','scrap','admin','coordinador'),
                    'list'	=> array('operador','tecnico','scrap','admin','gerente','coordinador'),
                    'edit'	=> array('operador','tecnico','admin','coordinador'),
                    'add'	=> array('operador','admin','coordinador'),
                    'shift'	=> array('admin','scrap','operador','gerente','coordinador'),
                    'getDeadCodeByGroup' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getDeadCodeByMachineId' => array('operador','tecnico','scrap','admin','coordinador'),
                    'isAnInactiveDeadTime' => array('operador','tecnico','scrap','admin','coordinador'),
                    'canAddDeadTime' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getDeadSection' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getDeadProblem' => array('operador','tecnico','scrap','admin','coordinador'),
                ),
                'DeadTime\Controller\Requisition' => array(
                    'index'	=> array('admin','gerente','dispatch'),
                    'export'	=> array('admin','dispatch'),
                    'exportt'	=> array('admin','dispatch'),
                    'exportPending'	=> array('admin','dispatch'),
                    'exportTech'	=> array('admin','dispatch'),
                    'pendings'	=> array('admin','gerente','dispatch'),
                    'pendingsp'	=> array('admin','gerente','dispatch'),
                    'techs'	=> array('admin','gerente','dispatch'),
                    'modify' => array('admin','dispatch'),
                    'isTechBusy' => array('admin','dispatch'),
                    'jsonList'	=> array('admin','gerente','dispatch'),
                ),
                'DeadTime\Controller\Work' => array(
                    'add'	=> array('admin','dispatch'),
                    'getMachineByArea'	=> array('admin','dispatch'),
                    'getTechByShift'	=> array('admin','dispatch'),
                    'workBreak'	=> array('admin','gerente','dispatch'),
                    'edit' => array('admin','dispatch'),
                    'index' => array('admin','gerente','dispatch'),
                    'jsonList'	=> array('admin','gerente','dispatch'),
                ),
                'ProductionModel\Controller\ProductionModel' => array(
                    'index'	=> array('operador','tecnico','scrap','admin','gerente','coordinador'),
                    'edit'	=> array('operador','admin','gerente','coordinador'),
                    'add'	=> array('operador','admin','coordinador'),
                    'replace' => array('admin','coordinador'),
                    'addByAjax' => array('operador','tecnico','scrap','admin','coordinador'),
                    'setNoProgram' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getStdProduction' => array('operador','tecnico','scrap','admin','coordinador'),
                    'isProductionShiftCaptured' => array('operador','tecnico','scrap','admin','coordinador'),
                    'isValidSku' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getProductionHours' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getProducts' => array('operador','tecnico','scrap','admin','coordinador'),
                    'geProductionFilteredPagination'  => array('operador','tecnico','scrap','admin','coordinador'),
                    'getProductsByShiftAjax' => array('operador','tecnico','scrap','admin','coordinador'),
                    'getProductSize' => array('operador','tecnico','scrap','admin','coordinador'),
                    'deleteProductionModelAjax' => array('admin','coordinador'),
                    'setNoProductionDtAjax'	=> array('operador','admin','coordinador'),
                    'hasReplaceModel' => array('admin','coordinador'),
                ),
                'Auth\Controller\Index' => array(
                    'index'	=> array('operador','tecnico','scrap','admin','anonimo','gerente','dispatch','coordinador'),
                    'login'	=> array('operador','tecnico','scrap','admin','anonimo','gerente','dispatch','coordinador'),
                    'logout'	=> array('operador','tecnico','scrap','admin','anonimo','gerente','dispatch','coordinador'),
                    'blocked'	=> array('operador','tecnico','scrap','admin','anonimo','gerente','dispatch','coordinador'),
                    'alogin'	=> array('operador','tecnico','scrap','admin','anonimo','gerente','dispatch','coordinador'),
                ),
                'Auth\Controller\Registration' => array(
                    'index'	=> array('admin'),
                    'edit'	=> array('admin'),
                    'deleteAjax'	=> array('admin'),
                    'registration-success' => array('admin'),
                ),
                'Auth\Controller\Admin' => array(
                    'list'	=> array('admin'),
                    'jsonList'	=> array('admin'),
                ),
                'Scrap\Controller\Scrap' => array(
                    'index'	=> array('operador','tecnico','scrap','admin','coordinador'),
                    'list'	=> array('operador','tecnico','scrap','admin','gerente','coordinador'),
                    'edit'	=> array('scrap','admin','coordinador'),
                    'add'	=> array('scrap','admin','coordinador'),
                    'dailyScrap'	=> array('scrap','admin','coordinador'),
                    'jsonList'	=> array('scrap','admin','coordinador'),
                    'shift'	=> array('scrap','admin','operador','gerente','coordinador'),
                    'getScrapDescription' => array('operador','tecnico','scrap','admin','coordinador'),
                ),
    
                 'Machine\Controller\Machine' => array(
                    'index' => array('operador','tecnico','scrap','admin'),
                    'add' => array('operador','tecnico','scrap','admin'),
                    'list'  => array('operador','tecnico','scrap','admin'),
                    'edit'  => array('operador','tecnico','scrap','admin'),
                     'jsonList'	=> array('admin'),
                
                ),

                 'Products\Controller\Products' => array(
                     'index' => array('operador','tecnico','scrap','admin'),
                     'add' => array('operador','tecnico','scrap','admin'),
                     'list'  => array('operador','tecnico','scrap','admin'),
                     'edit'  => array('operador','tecnico','scrap','admin'),
                     'jsonList'	=> array('admin'),
                     'deleteProductAjax'	=> array('admin')
                ),

                 'ScrapCode\Controller\ScrapCode' => array(
                    'index' => array('operador','tecnico','scrap','admin'),
                    'add' => array('operador','tecnico','scrap','admin'),
                    'foo' => array('operador','tecnico','scrap','admin'),
                    'list'  => array('operador','tecnico','scrap','admin'),
                    'edit'  => array('operador','tecnico','scrap','admin'),
                     'jsonList'	=> array('admin'),
                     'deleteScrapCodeAjax' => array('admin'),
                ),

                'Areas\Controller\Areas' => array(
                    'index' => array('admin'),
                    'create' => array('admin'),
                    'edit' => array('admin'),
                    'deleteSubArea' => array('admin'),
                    'assignSubArea' => array('admin'),
                    'editSubArea' => array('admin'),
                    'createCells'  => array('admin'),
                    'assignCells'  => array('admin'),
                    'list'  => array('admin'),
                    'detail'  => array('admin'),
                ),
            )
        )
    )
);
