<?php
namespace ProductionModel\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Expression;

use Zend\Session\Container as SessionContainer;

class NoProgramTable
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

    public function fetchById($id)
    {
        $rowSet = $this->tableGateway->select(array('id' => (int)$id));
        $row = $rowSet->current();
        return ($row)? $row : NULL;
    }

    public function getLastByMachine($machine_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($machine_id){
            $select->where(array('id_machine' => $machine_id));
            $select->where(array('is_active' => 1));
            $select->order('id desc');
        });

        $row = $resultSet->current();
        return ($row)? $row : NULL;
    }

    public function save(NoProgram $noProgram)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'id_machine' => $noProgram->id_machine,
            'is_active' => $noProgram->is_active,
        );

        $id = (int)$noProgram->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        }
        else
        {
            if ($this->fetchById($id))
            {
                $data["update_by"] = $usrId;
                $data["update_date"] = date('Y-m-d H:i:s');

                $this->tableGateway->update($data, array('id' => $id));
                return true;
            }
            else
            {
                throw new \Exception('NoProgram id does not exist');
            }
        }
    }
}