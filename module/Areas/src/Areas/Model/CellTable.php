<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 10/14/14
 * Time: 5:22 PM
 */

namespace Areas\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Session\Container as SessionContainer;

class CellTable {
    protected $tableGateway;
    protected $sessionBase;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->sessionBase = new SessionContainer('base');
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCellById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function delete($id)
    {
        $resultSet = $this->tableGateway->delete(array('id' => $id));
        return $resultSet;
    }

    public function deleteBySubAreaId($id_sub_area)
    {
        $resultSet = $this->tableGateway->delete(array('id_sub_area' => $id_sub_area));
        return $resultSet;
    }

    public function saveCell(Cell $cell)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'name' => $cell->name,
            'id_sub_area' => $cell->id_sub_area
        );

        $id = (int)$cell->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        }
        else
        {
            if ($this->getCellById($id))
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