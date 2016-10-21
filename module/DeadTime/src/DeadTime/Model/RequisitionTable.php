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

class RequisitionTable {

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

    public function fetchAllDetails()
    {
        $sql = "SELECT r.number, DATE(r.create_date) as create_date, (case r.free when 1 then 'Abierta' when 0 then 'Cerrada' end) as status,
                TIME(r.create_date) as create_hour, a.name as area_name,
                m.name as machine_name, pdt.problem, u.usr_name, (case r.machine_status when 1 then 'Activo' when 2 then 'Activo con Problemas' when 0 then 'Inactivo' end) as machine_status,
                r.cause, r.action, r.comments,
                IF(r.free = 1,TIMESTAMPDIFF(MINUTE,r.assign_time,r.fix_time),'') as acc_time
                FROM SICGA_Requisition r
                JOIN SICGA_Area a ON (r.id_area = a.id)
                JOIN SICGA_Machine m ON (r.id_machine = m.id)
                JOIN SICGA_Production_Dead_Time pdt ON (r.id_dead_time = pdt.id)
                JOIN users u ON (r.id_tech = u.usr_id)
                order by create_date desc";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function exportRequisitionData($start_date, $end_date)
    {
        $sql = "SELECT
                r.number AS number,
                r.create_date AS create_date,
                r.assign_time AS assign_date,
                r.fix_time AS fix_date,
                m.name AS machine_name,
                pdt.problem AS problem,
                u.usr_name AS tech_name,
                GROUP_CONCAT(ra.acc_time SEPARATOR ', ') AS tech_time_lst,
                (CASE r.generated_work  WHEN 1 THEN 'Si' WHEN 0 THEN 'No' END) AS generated_work,
                (CASE r.free  WHEN 1 THEN 'Si' WHEN 0 THEN 'No' END) AS free
                FROM
                sicga_requisition r
                JOIN sicga_machine m ON (r.id_machine = m.id)
                JOIN sicga_production_dead_time pdt ON (r.id_dead_time = pdt.id)
                LEFT JOIN users u ON (r.id_tech = u.usr_id)
                LEFT JOIN SICGA_Requisition_Assignment ra ON (r.number = ra.id_requisition)";

        if(!empty($start_date) && !empty($end_date))
            $sql .= " WHERE DATE(r.create_date) >= '$start_date' AND DATE(r.create_date) <= '$end_date'";
        else
            $sql .= " WHERE DATE(r.create_date) = '$start_date'";

        $sql .= " GROUP BY ra.id_requisition";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function exportTechData($start_date, $end_date)
    {
        $sql = "CALL GetTechPdfReport('".$start_date."','".$end_date."')";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function fetchByNumber($number)
    {

        $resultSet = $this->tableGateway->select(function(Select $select) use ($number){
            $select->columns(array('number','create_date','id_area','id_machine','id_shift','id_tech','assign_time',
            'comments','fix_time','id_dead_time','free','id_open_order'));
            $select->join(array('pdt' => 'SICGA_Production_Dead_Time'), 'SICGA_Requisition.id_dead_time = pdt.id',
                array('machine_status','problem','responsible','cause','action','other_problem'));
            $select->where(array('number' => $number));
        });

        return $resultSet->current();

    }

    public function getPendingList()
    {
        $resultSet = $this->tableGateway->select(function(Select $select) {
            $select->columns(array('number','create_date'));
            $select->join(array('m' => 'SICGA_Machine'), 'SICGA_Requisition.id_machine = m.id', array('machine_name' => 'name'));
            $select->join(array('dt' => 'SICGA_Production_Dead_Time'), 'SICGA_Requisition.id_dead_time = dt.id', array('problem'), 'left');
            $select->join(array('u' => 'users'), 'SICGA_Requisition.id_tech = u.usr_id', array('tech_name'=>'usr_name'), 'left');
            $select->where(array('free' => 0));

        });

        return $resultSet;
    }

    public function getRequisitionDetails($number)
    {
        $resultSet = $this->tableGateway->select(function(Select $select) use($number) {
            $select->columns(array('number','create_date','id_shift','assign_time','id_area','id_machine','id_tech'));
            $select->join(array('m' => 'SICGA_Machine'), 'SICGA_Requisition.id_machine = m.id', array('machine_name' => 'name'));
            $select->join(array('dt' => 'SICGA_Production_Dead_Time'), 'SICGA_Requisition.id_dead_time = dt.id', array('problem'), 'left');
            $select->join(array('u' => 'users'), 'SICGA_Requisition.id_tech = u.usr_id', array('tech_name'=>'usr_name'), 'left');
            $select->join(array('a' => 'SICGA_Area'), 'SICGA_Requisition.id_area = a.id', array('area_name'=>'name'));
            $select->where(array('number' => $number));

        });

        return $resultSet->current();
    }

    public function free(Requisition $requisition)
    {
        $data = array(
            'free' => 1,
            'fix_time' => $requisition->fix_time,
            'comments' => $requisition->comments,
            'id_open_order' => ($requisition->id_open_order) ? $requisition->id_open_order : null,
            'generated_work' => ($requisition->generated_work) ? $requisition->generated_work : 0,
        );

        $number = (int)$requisition->number;

        if ($this->fetchByNumber($number)) {
            $this->tableGateway->update($data, array('number' => $number));
        }
        else{
            throw new \Exception('Dead Code id does not exist');
        }
    }

    public function changeShift(Requisition $requisition)
    {
        $data = array(
            'free' => 0,
            'comments' => "Se paso al siguiente turno por medio de orden abierta.",
            'fix_time' => $requisition->fix_time,
            'id_open_order' => $requisition->id_open_order,
            'id_shift' => $requisition->id_shift,
        );

        $number = (int)$requisition->number;

        if ($this->fetchByNumber($number)) {
            $this->tableGateway->update($data, array('number' => $number));
        }
        else{
            throw new \Exception('Dead Code id does not exist');
        }
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

    public function saveRequisition(Requisition $requisition)
    {
        $data = array(
            'id_area' => $requisition->id_area,
            'id_machine' => $requisition->id_machine,
            'machine_status' => $requisition->machine_status,
            'problem' => $requisition->problem,
            'id_shift' => $requisition->id_shift,
            'id_tech' => $requisition->id_tech,
            'cause' => $requisition->cause,
            'action' => $requisition->action,
            'comments' => $requisition->comments,
            'assign_time' => $requisition->assign_time,
            'fix_time' => ($requisition->fix_time)? $requisition->fix_time : null,
            'id_dead_time' => $requisition->id_dead_time,
            'create_date' => $requisition->create_date,
            'free' => ($requisition->free)? $requisition->free : 0,
        );

        $number = (int)$requisition->number;

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
                return $number;
            }
            else
            {
                throw new \Exception('Dead Code id does not exist');
            }
        }
    }
} 