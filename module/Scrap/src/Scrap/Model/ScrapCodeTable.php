<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/16/14
 * Time: 9:36 PM
 */

namespace Scrap\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Session\Container as SessionContainer;

class ScrapCodeTable {
    protected $tableGateway;
    protected $sessionBase;

    public function __construct(TableGateway $tableGateway, Adapter $adapter)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
        $this->sessionBase = new SessionContainer('base');
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchAllByMachineId()
    {
        $machineId = $this->sessionBase->offsetGet('selMachineId');

        $resultSet = $this->tableGateway->select(array('machine_id' => $machineId, 'is_active' => 1));
        return $resultSet;
    }

    public function getScrapCodeById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return $row = null;
        }
        return $row;
    }
}