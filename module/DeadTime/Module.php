<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DeadTime;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use DeadTime\Model\DeadCode;
use DeadTime\Model\DeadCodeTable;
use DeadTime\Model\DeadCodeGroup;
use DeadTime\Model\DeadCodeGroupTable;
use DeadTime\Model\DeadProblem;
use DeadTime\Model\DeadProblemTable;
use DeadTime\Model\DeadSection;
use DeadTime\Model\DeadSectionTable;
use DeadTime\Model\ProductionDeadTime;
use DeadTime\Model\ProductionDeadTimeTable;
use DeadTime\Model\DeadCodeGroupMachine;
use DeadTime\Model\DeadCodeGroupMachineTable;
use DeadTime\Model\TechWork;
use DeadTime\Model\TechWorkTable;
use DeadTime\Model\RequisitionAssignment;
use DeadTime\Model\RequisitionAssignmentTable;
use Machine\Model\Machine;
use Machine\Model\MachineTable;
use DeadTime\Model\Requisition;
use DeadTime\Model\RequisitionTable;
use DeadTime\Model\OpenOrder;
use DeadTime\Model\OpenOrderTable;
use Areas\Model\Area;
use Areas\Model\AreaTable;
use Auth\Model\Auth;
use Auth\Model\UsersTable;

use ProductionModel\Model\ProductionModel;
use ProductionModel\Model\ProductionModelTable;
use ProductionModel\Model\Shift;
use ProductionModel\Model\ShiftTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
     //   var_dump(__DIR__ );exit;
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		            // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                    'Custom' => __DIR__ . '/../../vendor/Custom',
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
                'DeadTime\Model\RequisitionTable' =>  function($sm) {
                    $tableGateway = $sm->get('RequisitionTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new RequisitionTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'RequisitionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Requisition());
                    return new TableGateway('SICGA_Requisition', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\DeadCodeTable' =>  function($sm) {
                    $tableGateway = $sm->get('DeadCodeTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new DeadCodeTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'DeadCodeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeadCode());
                    return new TableGateway('SICGA_Death_Code', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\DeadProblemTable' =>  function($sm) {
                    $tableGateway = $sm->get('DeadProblemTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new DeadProblemTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'DeadProblemTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeadProblem());
                    return new TableGateway('SICGA_Death_Problem', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\DeadCodeGroupTable' =>  function($sm) {
                    $tableGateway = $sm->get('DeadCodeGroupTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new DeadCodeGroupTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'DeadCodeGroupTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeadCodeGroup());
                    return new TableGateway('SICGA_Death_Code_Group', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\DeadSectionTable' =>  function($sm) {
                    $tableGateway = $sm->get('DeadSectionTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new DeadSectionTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'DeadSectionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeadSection());
                    return new TableGateway('SICGA_Death_Section', $dbAdapter, null, $resultSetPrototype);
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

                'DeadTime\Model\DeadCodeGroupMachineTable' =>  function($sm) {
                    $tableGateway = $sm->get('DeadCodeGroupMachineTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new DeadCodeGroupMachineTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'DeadCodeGroupMachineTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeadCodeGroupMachine());
                    return new TableGateway('SICGA_Death_Code_Group_Machine', $dbAdapter, null, $resultSetPrototype);
                },
                'Areas\Model\AreaTable' =>  function($sm) {
                    $tableGateway = $sm->get('AreaTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new AreaTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'AreaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Area());
                    return new TableGateway('SICGA_Area', $dbAdapter, null, $resultSetPrototype);
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
                'Auth\Model\UsersTable' =>  function($sm) {
                    $tableGateway = $sm->get('UsersTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UsersTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'UsersTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Auth()); // Notice what is set here
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\TechWorkTable' =>  function($sm) {
                    $tableGateway = $sm->get('TechWorkTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new TechWorkTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'TechWorkTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TechWork());
                    return new TableGateway('SICGA_Tech_Work', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\RequisitionAssignmentTable' =>  function($sm) {
                    $tableGateway = $sm->get('RequisitionAssignmentTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new RequisitionAssignmentTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'RequisitionAssignmentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RequisitionAssignment());
                    return new TableGateway('SICGA_Requisition_Assignment', $dbAdapter, null, $resultSetPrototype);
                },
                'DeadTime\Model\OpenOrderTable' =>  function($sm) {
                    $tableGateway = $sm->get('OpenOrderTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new OpenOrderTable($tableGateway,$dbAdapter);
                    return $table;
                },
                'OpenOrderTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OpenOrder());
                    return new TableGateway('SICGA_Open_Order', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
