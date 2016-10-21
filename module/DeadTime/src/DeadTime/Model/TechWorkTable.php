<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/12/14
 * Time: 10:18 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Custom\DataTables;

class TechWorkTable {

    protected $tableGateway;
    protected $sessionBase;

    public function __construct(TableGateway $tableGateway, Adapter $adapter)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
    }

    public function fetchByNumber($number)
    {
        $number  = (int) $number;
        $rowSet = $this->tableGateway->select(array('number' => $number));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(function(Select $select) {
            $select->columns(array('number','type','total','comments','create_date','free'));
            $select->join(array('a' => 'SICGA_Area'), 'SICGA_Tech_Work.id_area = a.id', array('area_name' => 'name'));
            $select->join(array('m' => 'SICGA_Machine'), 'SICGA_Tech_Work.id_machine = m.id', array('machine_name'=>'name'));
            $select->join(array('s' => 'SICGA_Shift'), 'SICGA_Tech_Work.id_shift = s.id', array('shift_name'=>'name'));
            $select->join(array('u' => 'users'), 'SICGA_Tech_Work.id_tech = u.usr_id', array('usr_name'));
            $select->order('number DESC');
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

    public function saveTechWork(TechWork $techWork)
    {
        $data = array(
            'type' => $techWork->type,
            'id_area' => $techWork->id_area,
            'id_machine' => $techWork->id_machine,
            'id_shift' => $techWork->id_shift,
            'id_tech' => $techWork->id_tech,
            'total' => $techWork->total,
            'crono' => $techWork->crono,
            'comments' => $techWork->comments,
            'create_date' => $techWork->create_date,
            'update_date' => $techWork->update_date,
            'stop_date' => $techWork->stop_date,
            'stopped' => (empty($techWork->stopped))? 0 : $techWork->stopped,
            'free' => (empty($techWork->free))? 0 : $techWork->free,
        );

        $number = (int)$techWork->number;

        if ($number == 0)
        {
            $this->tableGateway->insert($data);

            return $this->tableGateway->lastInsertValue;
        }
        else
        {
            if ($this->fetchByNumber($number))
            {
                $this->tableGateway->update($data, array('number' => $number));
            }
            else
            {
                throw new \Exception('Dead Code id does not exist');
            }
        }
    }
}