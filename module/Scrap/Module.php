<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Scrap;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Scrap\Model\ScrapCode;
use Scrap\Model\ScrapCodeTable;
use Scrap\Model\Scrap;
use Scrap\Model\ScrapTable;
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
                'Scrap\Model\ScrapCodeTable' =>  function($sm) {
                        $tableGateway = $sm->get('ScrapCodeTableGateway');
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new ScrapCodeTable($tableGateway, $dbAdapter);
                        return $table;
                    },
                'ScrapCodeTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new ScrapCode());
                        return new TableGateway('SICGA_Scrap_Code', $dbAdapter, null, $resultSetPrototype);
                    },
                'Scrap\Model\ScrapTable' =>  function($sm) {
                        $tableGateway = $sm->get('ScrapTableGateway');
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new ScrapTable($tableGateway, $dbAdapter);
                        return $table;
                    },
                'ScrapTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Scrap());
                        return new TableGateway('SICGA_Scrap', $dbAdapter, null, $resultSetPrototype);
                    },
            ),
        );
    }

}
