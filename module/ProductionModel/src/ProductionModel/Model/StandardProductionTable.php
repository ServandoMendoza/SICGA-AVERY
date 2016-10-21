<?php
namespace ProductionModel\Model;

use Zend\Db\TableGateway\TableGateway;
use Custom\DataTables;

use Zend\Session\Container as SessionContainer;

class StandardProductionTable
{
    private $tableGateway;
    private $sessionBase;

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

    public function getStandardProductionById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function isRepeatedSize($arrData)
    {
        $rowSet = $this->tableGateway->select(
            array(
                'machine_id' => $arrData['machine_id'],
                'size' => $arrData['size']
            )
        );

        $row = $rowSet->count();

        return ($row)? true: false;
    }

    //Array single row filter
    public function getStandardProductionByFilter($arrFilter)
    {
        $rowSet = $this->tableGateway->select($arrFilter);
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    //Array row filter
    public function getStandardProductionsByFilter($arrFilter)
    {
        $rowSet = $this->tableGateway->select($arrFilter);
        return $rowSet;
    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings );

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS name, size, cycles_per_minute,products_per_hour, machine_runtime,
              crew_size,  p.id as id
			 FROM $table p JOIN SICGA_Machine m on (p.machine_id = m.id)
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function deleteStandardProduction($id)
    {
        $resultSet = $this->tableGateway->delete(array('id' => $id));
        return $resultSet;
    }

    public function saveStandardProduction(StandardProduction $standardProduction)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'machine_id' => $standardProduction->machine_id,
            'size' => $standardProduction->size,
            'cycles_per_minute' => $standardProduction->cycles_per_minute,
            'products_per_hour' => $standardProduction->products_per_hour,
            'machine_runtime' => $standardProduction->machine_runtime,
            'crew_size' => $standardProduction->crew_size,
            'labor_runtime' => $standardProduction->labor_runtime,
            'indirect_crew' => $standardProduction->indirect_crew
        );

        $id = (int)$standardProduction->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getStandardProductionById($id))
            {
                $data["update_by"] = $usrId;

                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Form id does not exist');
            }
        }
    }
}
