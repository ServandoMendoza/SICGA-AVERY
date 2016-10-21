<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/30/14
 * Time: 4:30 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Session\Container as SessionContainer;

class OpenOrderTable {
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

    public function fetchAllById($id)
    {
        $resultSet = $this->tableGateway->select(array('id' => $id));

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function saveOpenOrder(OpenOrder $OpenOrder)
    {
        $data = array(
            'details' => $OpenOrder->details,
            'next_shift_plan' => $OpenOrder->next_shift_plan,
            'recommendations' => $OpenOrder->recommendations,
            'problem_solution' => $OpenOrder->problem_solution
        );

        $id = (int)$OpenOrder->id;

        if ($id == 0)
        {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        }
        else
        {
            if ($this->fetchAllById($id))
            {
                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Dead Code id does not exist');
            }
        }
    }
} 