<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 10/3/14
 * Time: 8:40 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class DeadCodeGroupMachineTable {
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway, Adapter $adapter)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchAllByGroupId($death_code_group_id)
    {
        $resultSet = $this->tableGateway->select(
            array('death_code_group_id' => $death_code_group_id)
        );

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function existantValue($arrData)
    {
        $resultSet = $this->tableGateway->select(
            array(
                'death_code_group_id' => $arrData['death_code_group_id'],
                'machine_id' => $arrData['machine_id'])
        );

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }

        return $row;
    }


    public function saveDeadCodeGroupMachine($arrData)
    {
        //var_dump($arrData);exit;

        $data = array(
            'death_code_group_id' => $arrData['death_code_group_id'],
            'machine_id' => $arrData['machine_id']
        );

        //$deadCodeGroupMachineDM = $this->existantValue($data);

        //if($deadCodeGroupMachineDM)

        $this->tableGateway->insert($data);
    }

} 