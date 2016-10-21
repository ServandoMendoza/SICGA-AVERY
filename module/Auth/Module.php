<?php
namespace Auth; 

// Add this for Table Date Gateway
use Auth\Model\Auth;
use Auth\Model\UsersTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Machine\Model\Machine;
use Machine\Model\MachineTable;

// Add this for SMTP transport
use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
				// For Yable data Gateway
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
				// Add this for SMTP transport
				'mail.transport' => function (ServiceManager $serviceManager) {
					$config = $serviceManager->get('Config'); 
					$transport = new Smtp();                
					$transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
					return $transport;
				},
            ),
        );
    }		
}