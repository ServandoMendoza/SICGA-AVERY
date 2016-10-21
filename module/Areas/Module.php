<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Areas;

use Areas\Model\Cell;
use Areas\Model\CellTable;
use Areas\Model\CellMachine;
use Areas\Model\CellMachineTable;
use Areas\Model\Area;
use Areas\Model\AreaTable;
use Areas\Model\SubArea;
use Areas\Model\SubAreaTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Machine\Model\Machine;
use Machine\Model\MachineTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface
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

                'Areas\Model\CellTable' =>  function($sm) {
                    $tableGateway = $sm->get('CellTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CellTable($tableGateway, $dbAdapter);
                    return $table;
                },

                'CellTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Cell());
                    return new TableGateway('SICGA_Cell', $dbAdapter, null, $resultSetPrototype);
                },

                'Areas\Model\SubAreaTable' =>  function($sm) {
                    $tableGateway = $sm->get('SubAreaTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SubAreaTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'SubAreaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SubArea());
                    return new TableGateway('SICGA_Sub_Area', $dbAdapter, null, $resultSetPrototype);
                },

                'Areas\Model\CellMachineTable' =>  function($sm) {
                    $tableGateway = $sm->get('CellMachineTableGateway');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CellMachineTable($tableGateway, $dbAdapter);
                    return $table;
                },
                'CellMachineTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CellMachine());
                    return new TableGateway('SICGA_Cell_Machine', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
