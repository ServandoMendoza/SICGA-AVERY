<?php
namespace ProductionModel\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;


class ShiftTable
{
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

    public function getShiftById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getShiftByTime($timeNow)
    {
        $sql = "SELECT number,start_hour,end_hour,
                CASE number
                    WHEN '1' THEN 'Matutino'
                    WHEN '2' THEN 'Vespertino'
                    WHEN '3' THEN 'Nocturno' END as name
                FROM SICGA_Shift
                WHERE (start_hour <= end_hour AND '$timeNow' 
                        BETWEEN 
                        start_hour AND end_hour) 
                OR (end_hour < start_hour AND '$timeNow' 
                    NOT BETWEEN 
                    end_hour AND start_hour)";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result->current();
    }
}
