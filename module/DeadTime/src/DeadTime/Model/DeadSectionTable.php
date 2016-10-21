<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 8/20/14
 * Time: 12:52 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate;

class DeadSectionTable {
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

    public function fetchAllByParentCodeGroupId($dead_code_group_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select) use ($dead_code_group_id){
            $select->columns(array('id','name','code'));
            $select->where(array('dead_code_group_id' => $dead_code_group_id));
        });

        return $resultSet;
    }

} 