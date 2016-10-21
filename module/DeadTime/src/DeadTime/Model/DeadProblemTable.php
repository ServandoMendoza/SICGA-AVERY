<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 8/20/14
 * Time: 2:36 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate;


class DeadProblemTable {
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

    public function getProblemById($subcode_id)
    {
        $resultSet = $this->tableGateway->select(array('id' => $subcode_id));
        return $resultSet->current();
    }

    public function fetchAllBySectionId($subgroup_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select) use ($subgroup_id){
            $select->columns(array('id','description','code'));
            $select->where(array('death_section_id' => $subgroup_id));
        });

        return $resultSet;
    }

    public function fetchAllByDeadCodeId($death_code_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select) use ($death_code_id){
            $select->columns(array('id','description','code'));
            $select->where(array('death_code_id' => $death_code_id));
        });

        return $resultSet;
    }
} 