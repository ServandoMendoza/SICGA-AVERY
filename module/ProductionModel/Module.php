<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ProductionModel;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use ProductionModel\Model\StandardProduction;
use ProductionModel\Model\StandardProductionTable;
use ProductionModel\Model\Shift;
use ProductionModel\Model\ShiftTable;
use ProductionModel\Model\Product;
use ProductionModel\Model\ProductTable;
use ProductionModel\Model\ProductionModel;
use ProductionModel\Model\ProductionModelTable;
use ProductionModel\Model\NoProgram;
use ProductionModel\Model\NoProgramTable;
use DeadTime\Model\ProductionDeadTime;
use DeadTime\Model\ProductionDeadTimeTable;
use Machine\Model\Machine;
use Machine\Model\MachineTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ProductionModel\Model\StandardProductionTable' =>  function($sm) {
                    $tableGateway = $sm->get('StandardProductionTableGateway');
                    $table = new StandardProductionTable($tableGateway);
                    return $table;
                },
                'StandardProductionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new StandardProduction());
                    return new TableGateway('SICGA_Standard_Production', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductionModel\Model\ShiftTable' =>  function($sm) {
                    $tableGateway = $sm->get('ShiftTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ShiftTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'ShiftTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Shift());
                    return new TableGateway('SICGA_Shift', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductionModel\Model\ProductTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProductTableGateway');
                    $table = new ProductTable($tableGateway);
                    return $table;
                },
                'ProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Product());
                    return new TableGateway('SICGA_Product', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductionModel\Model\ProductionModelTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProductionModelTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ProductionModelTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'ProductionModelTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductionModel());
                    return new TableGateway('SICGA_Production_Model', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\ProductionDeadTimeTable' =>  function($sm) {
                        $tableGateway = $sm->get('ProductionDeadTimeTableGateway');
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new ProductionDeadTimeTable($tableGateway,$dbAdapter);
                        return $table;
                    },
                'ProductionDeadTimeTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new ProductionDeadTime());
                        return new TableGateway('SICGA_Production_Dead_Time', $dbAdapter, null, $resultSetPrototype);
                    },
                'Machine\Model\MachineTable' =>  function($sm) {
                    $tableGateway = $sm->get('MachineTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new MachineTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'MachineTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Machine());
                    return new TableGateway('SICGA_Machine', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductionModel\Model\NoProgramTable' =>  function($sm) {
                    $tableGateway = $sm->get('NoProgramTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new NoProgramTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'NoProgramTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new NoProgram());
                    return new TableGateway('SICGA_No_Program', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
