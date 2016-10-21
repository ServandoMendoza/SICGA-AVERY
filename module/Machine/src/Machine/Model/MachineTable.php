<?php
namespace Machine\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Custom\DataTables;

use Zend\Session\Container as SessionContainer;

class MachineTable
{
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

    public function getMachineById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getMachineByAreaId($idArea)
    {
        $sql = "SELECT m.id, m.name FROM sicga_cell_machine cm
                JOIN sicga_cell c ON (cm.id_cell = c.id)
                JOIN sicga_sub_area sa ON (c.id_sub_area = sa.id)
                JOIN sicga_area a ON (sa.id_area = a.id)
                JOIN sicga_machine m ON (cm.id_machine = m.id)
                WHERE a.id = $idArea";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function getMachines()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->columns(array('*'));
        });

        return $resultSet;
    }

    public function getMachinesWithDcGroup()
    {
        $resultSet = $this->tableGateway->select(function(Select $select ){
            $select->columns(array('id','name'));
            $select->join(array('dcgm' => 'SICGA_Death_Code_Group_Machine'), 'SICGA_Machine.id = dcgm.machine_id', array());
            $select->group(array('id'));
        });

        return $resultSet;
    }

    public function isMachineInArea($id_machine, $id_sub_area, $is_edit)
    {
        $sql = "SELECT count(cm.id) as count FROM SICGA_Cell_Machine cm
                JOIN SICGA_Cell c ON (cm.id_cell = c.id)
                JOIN SICGA_Sub_Area sa ON (c.id_sub_area = sa.id)
                JOIN SICGA_AREA a ON (sa.id_area = a.id)";

        if(!empty($is_edit))
            $sql .= " WHERE sa.id <> $id_sub_area AND cm.id_machine = $id_machine;";
        else
            $sql .= " WHERE cm.id_machine = $id_machine;";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result->current();
    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings);

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS name, model, year, id
			 FROM $table
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function saveMachine(Machine $Machine)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');
        
        $data = array(
            'name' => $Machine->name,
            'model' => $Machine->model
        );

        $id = (int)$Machine->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;
            $data["year"] = date('Y');

            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getMachineById($id))
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
