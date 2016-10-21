<?php
namespace ProductionModel\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Expression;

class ProductTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getProductById($sku)
    {
        $rowSet = $this->tableGateway->select(array('sku' => $sku));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getProductionsPlanBySkuTerm($skuTerm)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($skuTerm){
            $select->columns(array('sku' => new Expression("substring(sku , 6)"),'size'));
            $select->where->like('sku', $skuTerm.'%');
            $select->where(array('is_active' => 1));
        });

        return $resultSet;
    }

    public function getProductsSkuPrefix()
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) {
            $select->columns(array('sku_prefix' => new Expression("LEFT(sku , 5)")));
            $select->group('sku_prefix');
            $select->order('sku_prefix ASC');
        });

        return $resultSet;

    }
}
