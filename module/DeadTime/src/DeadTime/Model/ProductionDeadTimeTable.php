<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 11:36 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Expression;

use Zend\Session\Container as SessionContainer;

class ProductionDeadTimeTable {
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

    public function isAnInactiveDeadTime($production_model_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($production_model_id){
            $select->columns(array('count' => new Expression("count(*)")));
            $select->where(array('machine_status' => 0));
            $select->where(array('production_model_id' => $production_model_id));
        });

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getProductionDeadTimeListByProdModelId($production_model_id)
    {
        $production_model_id  = (int) $production_model_id;

        $resultSet = $this->tableGateway->select(function(Select $select) use ($production_model_id){
            $select->columns(array('id','production_model_id','responsible','cause','responsible','action','section','problem','machine_status','dead_time','create_date','death_problem_id','other_problem'));
            $select->join(array('dc' => 'SICGA_Death_Code'), 'SICGA_Production_Dead_Time.death_code_id = dc.id', array('code', 'death_code_id' => 'id'));
            $select->join(array('dcg' => 'SICGA_Death_Code_group'), 'dc.death_code_group_id = dcg.id', array('name'));
            $select->join(array('dp' => 'SICGA_Death_Problem'), 'SICGA_Production_Dead_Time.death_problem_id = dp.id', array('dp_description' => 'description'),'left');
            $select->join(array('ds' => 'SICGA_Death_Section'), 'dp.death_section_id = ds.id', array('ds_name' => 'name'),'left');
            $select->where(array('production_model_id' => $production_model_id));
            $select->order('SICGA_Production_Dead_Time.create_date DESC');
        });



        return $resultSet;
    }

    public function getProductionDeadTimeListByShiftDate($shiftNumber,$unixDate)
    {
        $machine_id = $this->sessionBase->offsetGet('selMachineId');

        $resultSet = $this->tableGateway->select(function(Select $select) use ($shiftNumber, $unixDate, $machine_id){
            $select->columns(array('id','section','problem','dead_time','machine_status','death_problem_id'));
            $select->join(array('pm' => 'SICGA_Production_Model'), 'SICGA_Production_Dead_Time.production_model_id = pm.id', array('product_sku','start_hour','end_hour'));
            $select->join(array('dp' => 'SICGA_Death_Problem'), 'SICGA_Production_Dead_Time.death_problem_id = dp.id', array('dp_description' => 'description'),'left');
            $select->join(array('ds' => 'SICGA_Death_Section'), 'dp.death_section_id = ds.id', array('ds_name' => 'name'),'left');
            $select->where(array('pm.shift_id' => $shiftNumber));
            $select->where(array('pm.machine_id' => $machine_id));
            $select->where->expression("UNIX_TIMESTAMP(date(pm.create_date)) = ?", array($unixDate));
            $select->order('pm.start_hour');
        });

        return $resultSet;
    }

    public function getProductionDeadTime($id)
    {
        $id  = (int) $id;

        $resultSet = $this->tableGateway->select(function(Select $select ) use ($id){
            $select->columns(array('id', 'production_model_id','death_code_id','cause','responsible','action','section','problem','machine_status','create_date','death_problem_id','others_responsible','other_problem'));
            $select->join('SICGA_Death_Code', 'SICGA_Production_Dead_Time.death_code_id = SICGA_Death_Code.id', array('death_code_group_id'));
            $select->where(array('SICGA_Production_Dead_Time.id' => $id));
        });

        $row = $resultSet->current();
        if (!$row) {
            //throw new \Exception("Could not find row $id");
            $row = null;
        }
        return $row;
    }

    public function saveProductionDeadTime(ProductionDeadTime $productionDeadTime)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'production_model_id' => $productionDeadTime->production_model_id,
            'death_code_id' => $productionDeadTime->death_code_id,
            'cause' => $productionDeadTime->cause,
            'responsible' => $productionDeadTime->responsible,
            'action' => $productionDeadTime->action,
            'section' => $productionDeadTime->section,
            'problem' => $productionDeadTime->problem,
            'machine_status' => $productionDeadTime->machine_status,
            'death_problem_id' => $productionDeadTime->death_problem_id,
            'other_problem' => $productionDeadTime->other_problem,
            'others_responsible' => $productionDeadTime->others_responsible,
        );

        $id = (int)$productionDeadTime->id;
        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        }
        else
        {
            if ($this->getProductionDeadTime($id))
            {
                // Actualizar el tiempo en base al update, es el tiempo que duro muerto...
                $delta_time = time() - strtotime($productionDeadTime->create_date);
                $delta_time %= 3600;
                $minutes = $delta_time / 60;

                $data['dead_time'] = $minutes;
                $data['update_date'] =  date('Y-m-d H:i:s');
                $data["update_by"] = $usrId;

                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Production Dead Time id does not exist');
            }
        }
    }

} 