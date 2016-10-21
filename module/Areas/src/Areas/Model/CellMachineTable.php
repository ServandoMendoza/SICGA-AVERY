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

class CellMachineTable {
    protected $tableGateway;
    protected $sessionBase;

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

    public function getCellMachineById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function deleteCellMachineBySubArealId($id_sub_area)
    {
        $sql = "DELETE FROM SICGA_Cell_Machine WHERE id_cell in
                (SELECT id FROM SICGA_Cell where id_sub_area = $id_sub_area);";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function getCellMachineByCell($id_cell)
    {
        $sql = "SELECT m.* FROM SICGA_Cell_Machine cm
                LEFT JOIN SICGA_Machine m ON (cm.id_machine = m.id)
                WHERE cm.id_cell =  $id_cell";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function saveCellMachine(CellMachine $cellMachine)
    {
        $data = array(
            'id_cell' => $cellMachine->id_cell,
            'id_machine' => $cellMachine->id_machine
        );

        $id = (int)$cellMachine->id;

        if ($id == 0)
        {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        }
        else
        {
            if ($this->getCellMachineById($id))
            {
                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Form id does not exist');
            }
        }
    }
} 