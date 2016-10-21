<?php
namespace Scrap\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Custom\DataTables;

use Zend\Session\Container as SessionContainer;

class ScrapTable
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

    public function getScrapById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getScrapByProdModelId($production_model_id)
    {
        $production_model_id  = (int) $production_model_id;
        $resultSet = $this->tableGateway->select(function(Select $select) use ($production_model_id){
            $select->columns(array('id','quantity','percentage','create_date'));
            $select->join(array('sc' => 'SICGA_Scrap_Code'), 'SICGA_Scrap.scrap_code_id = sc.id', array('code','description',));
            $select->where(array('production_model_id' => $production_model_id));
        });

        return $resultSet;
    }

    public function getScrapByProdModelIdShift($shiftNumber,$unixDate)
    {
        $machine_id = $this->sessionBase->offsetGet('selMachineId');

        $resultSet = $this->tableGateway->select(function(Select $select) use ($shiftNumber, $unixDate, $machine_id){
            $select->columns(array('id','quantity','percentage','create_date'));
            $select->join(array('sc' => 'SICGA_Scrap_Code'), 'SICGA_Scrap.scrap_code_id = sc.id', array('code','description',));
            $select->join(array('pm' => 'SICGA_Production_Model'), 'SICGA_Scrap.production_model_id = pm.id', array('product_sku','start_hour','end_hour'));
            $select->where(array('pm.shift_id' => $shiftNumber));
            $select->where(array('pm.machine_id' => $machine_id));
            $select->where->expression("UNIX_TIMESTAMP(date(pm.create_date)) = ?", array($unixDate));
            $select->order('pm.start_hour');
        });

        return $resultSet;
    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();
        $machine_id = $this->sessionBase->offsetGet('selMachineId');

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings );

        if(!empty($where)){
            $where .= " AND machine_id = ".$machine_id;
        }
        else{
            $where .= " WHERE machine_id = ".$machine_id;
        }

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS *
                    FROM $table
			        $where
			        $order
			        $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function saveScrap(Scrap $Scrap)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'production_model_id' => $Scrap->production_model_id,
            'scrap_code_id' => $Scrap->scrap_code_id,
            'quantity' => $Scrap->quantity,
            'percentage' => $Scrap->percentage
        );

        $id = (int)$Scrap->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getScrapById($id))
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
