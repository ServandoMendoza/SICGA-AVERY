<?php
namespace Products\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Custom\DataTables;

use Zend\Session\Container as SessionContainer;

class ProductsTable
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

    public function getProductsBySku($sku)
    {
        $sku  =  $sku;
        $rowSet = $this->tableGateway->select(array('sku' => $sku));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getProducts()
    {
        $resultSet = $this->tableGateway->select(function(Select $select){
            $select->columns(array('*'));
        });

        return $resultSet;
    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings);

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS sku, description, size, create_date
			 FROM $table
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function deleteProduct($id)
    {
        if ($this->getProductsBySku($id)) {
            $this->tableGateway->update(array('is_active' => 0), array('sku' => $id));
        }
        else {
            throw new \Exception('Product sku does not exist');
        }
    }

    public function saveProducts(Products $Products)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');
        
        $data = array(
            'sku' => $Products->sku,
            'name' => $Products->name,
            'description' => $Products->description,
            'size' => $Products->size
        );

        $productsDM = $this->getProductsBySku($Products->sku);
        $sku =  $productsDM->sku;

        if ($sku == 0)
        {
            $data["create_by"] = $usrId;


            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getProductsBySku($sku))
            {
                $data["update_by"] = $usrId;
                $data["update_date"] = date('Y-m-d H:i:s');

            
                $this->tableGateway->update($data, array('sku' => $sku));

            }
            else
            {
                throw new \Exception('Form sku does not exist');
            }
        }
    }
}
