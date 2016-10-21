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
use Custom\DataTables;
use Zend\Session\Container as SessionContainer;

class AreaTable {
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

    public function getAreaById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getAreaByMachineId($idMachine)
    {
        $idMachine  = (int) $idMachine;

        $resultSet = $this->tableGateway->select(function(Select $select ) use ($idMachine){
            //$select->columns(array('id', 'name'));
            $select->join(array('sa' => 'SICGA_Sub_Area'), 'SICGA_Area.id = sa.id_area',array());
            $select->join(array('c' => 'SICGA_Cell'), 'sa.id = c.id_sub_area',array());
            $select->join(array('cm' => 'SICGA_Cell_Machine'), 'c.id = cm.id_cell',array());
            $select->where(array('cm.id' => $idMachine));
        });

        $row = $resultSet->current();
        if (!$row) {
            //throw new \Exception("Could not find row $id");
            $row = null;
        }
        return $row;
    }


    public function saveArea(Area $area)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'name' => $area->name
        );

        $id = (int)$area->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        }
        else
        {
            if ($this->getAreaById($id))
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