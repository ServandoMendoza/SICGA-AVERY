<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/27/14
 * Time: 4:23 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Session\Container as SessionContainer;

class RequisitionAssignmentTable {

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

    public function fetchAllByReqId($req_id)
    {
        $resultSet = $this->tableGateway->select(array('id_requisition' => $req_id));
        return $resultSet;

    }

    public function saveRequisitionAssignment(RequisitionAssignment $RequisitionAssignment)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'id_requisition' => $RequisitionAssignment->id_requisition,
            'id_tech' => $RequisitionAssignment->id_tech,
            'acc_time' => $RequisitionAssignment->acc_time,
            'assign_date' => $RequisitionAssignment->assign_date,
        );

        $id = (int)$RequisitionAssignment->id;

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