<?php
namespace ScrapCode\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Custom\DataTables;

use Zend\Session\Container as SessionContainer;


class ScrapCodeTable
{
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

    public function getScrapCodeById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getScrapCode()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->columns(array('*'));
        });

        return $resultSet;
    }

  /*  public function getProductionsPlanBySkuTerm($skuTerm)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($skuTerm){
            $select->columns(array('sku' => new Expression("substring(sku , 6)"),'size'));
            $select->where->like('sku', $skuTerm.'%');
        });

        return $resultSet;
    } */

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
        $where = DataTables::filter( $request, $columns, $bindings, 'sc.' );

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS * FROM $table
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function deleteScrapCode($id)
    {
        if ($this->getScrapCodeById($id)) {
            $this->tableGateway->update(array('is_active' => 0), array('id' => $id));
        }
        else {
            throw new \Exception('Scrap Code id does not exist');
        }
    }

    public function saveScrapCode(ScrapCode $ScrapCode)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');
        
        $data = array(
            'code' => $ScrapCode->code,
            'description' => $ScrapCode->description,
            'machine_id' => $ScrapCode->machine_id
        );

        $id = (int)$ScrapCode->id;
        

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getScrapCodeById($id))
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
