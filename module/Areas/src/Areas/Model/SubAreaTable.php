<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 10/14/14
 * Time: 5:22 PM
 */

namespace Areas\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Custom\DataTables;
use Zend\Session\Container as SessionContainer;

class SubAreaTable {
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

    public function getSubAreaById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getDetailByAreaId($id_area)
    {
        $sql = "SELECT sa.id AS sub_area_id, sa.name as sub_area_name,
                c.id as cell_id, c.name as cell_name, cm.id as cell_machine_id,
                m.name as machine_name
                FROM SICGA_Sub_Area sa
                LEFT JOIN SICGA_Cell c ON (sa.id = c.id_sub_area)
                LEFT JOIN SICGA_Cell_Machine cm ON (cm.id_cell = c.id)
                LEFT JOIN SICGA_Machine m ON(m.id = cm.id_machine)
                WHERE sa.id_area = $id_area";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function getCellsSubArea($id_sub_area)
    {
        $sql = "SELECT c.*, sa.id_area FROM SICGA_Cell c
                JOIN SICGA_Sub_Area sa ON (c.id_sub_area = sa.id)
                WHERE sa.id = $id_sub_area";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function delete($id)
    {
        $resultSet = $this->tableGateway->delete(array('id' => $id));
        return $resultSet;
    }

    public function saveSubArea(SubArea $subArea)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'name' => $subArea->name,
            'id_area' => $subArea->id_area
        );

        $id = (int)$subArea->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        }
        else
        {
            if ($this->getSubAreaById($id))
            {
                $data["update_by"] = $usrId;
                $data["update_date"] = date('Y-m-d H:i:s');

                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Form id does not exist');
            }
        }
    }
} 