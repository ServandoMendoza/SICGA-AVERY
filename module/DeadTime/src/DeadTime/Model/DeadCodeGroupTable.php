<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:42 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Predicate\Expression;
use Custom\DataTables;


use Zend\Session\Container as SessionContainer;

class DeadCodeGroupTable
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

    public function getDeadCodeGroupByName($name)
    {
        $name = strtoupper(trim(str_replace(' ', '', $name)));

        $resultSet = $this->tableGateway->select(function (Select $select) use ($name) {
            $select->columns(array('count' => new Expression("count(*)")));
            $select->where->expression("UPPER(TRIM(REPLACE(name,' ',''))) = ?", array($name));

        });

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getDeadCodeGroupById($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getDeadCodeGroupByMachineId($machine_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($machine_id){
            $select->columns(array('id', 'name'));
            $select->join(array('dcgm' => 'SICGA_Death_Code_Group_Machine'), 'SICGA_Death_Code_Group.id = dcgm.death_code_group_id', array('machine_id'), 'left');

            if(empty($machine_id))
            {
                $select->where(new Predicate\IsNull('dcgm.machine_id'));
            }
            else{
                $select->where(array('dcgm.machine_id' => $machine_id));
            }
        });


        return $resultSet;
    }

    public function fetchAllByMachineId()
    {
        $machineId = $this->sessionBase->offsetGet('selMachineId');

        $resultSet = $this->tableGateway->select(function (Select $select) use ($machineId) {
            $select->columns(array('id', 'name'));
            $select->join(array('gcp' => 'SICGA_Death_Code_Group_Machine'), 'SICGA_Death_Code_Group.id = gcp.death_code_group_id', array(), 'left');
            $select->where(array('gcp.machine_id' => $machineId));
            $select->where(array('SICGA_Death_Code_Group.is_active' => 1));
            $select->where(new Predicate\IsNull('gcp.machine_id'), 'OR');
        });

        return $resultSet;

    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings );

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS *
			 FROM $table
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function deleteDeadCodeGroup($id)
    {
        if ($this->getDeadCodeGroupById($id)) {
            $this->tableGateway->update(array('is_active' => 0), array('id' => $id));
        }
        else {
            throw new \Exception('Dead Code Group id does not exist');
        }
    }

    public function saveDeadCodeGroup(DeadCodeGroup $deadCodeGroup)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'name' => $deadCodeGroup->name,
            'description' => $deadCodeGroup->description
        );

        $id = (int)$deadCodeGroup->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;
            $this->tableGateway->insert($data);

            return (int)$this->tableGateway->lastInsertValue;
        }
        else
        {
            if ($this->getDeadCodeGroupById($id))
            {
                $data['update_date'] =  date('Y-m-d H:i:s');
                $data["update_by"] = $usrId;

                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Dead Code id does not exist');
            }
        }
    }
} 