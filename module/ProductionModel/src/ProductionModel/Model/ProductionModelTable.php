<?php
namespace ProductionModel\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Expression;

use Zend\Session\Container as SessionContainer;

class ProductionModelTable
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

    public function getCrudeProductionModelById($id)
    {
        $id  = (int) $id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function canAddDeadTime($production_model_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($production_model_id){
            $select->columns(array('count' => new Expression("COUNT(*)")));
            $select->where("CURTIME() BETWEEN  start_hour AND end_hour");
            $select->where(array('id' => $production_model_id));
        });

        $row = $resultSet->current();

        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function hasReplaceModel($production_model_id)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($production_model_id){
            $select->columns(array('count' => new Expression("COUNT(*)")));
            $select->where(array('replaced_prod_id' => $production_model_id));
        });

        $row = $resultSet->current();

        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getProductionModelById($id)
    {
        $id  = (int) $id;

        $resultSet = $this->tableGateway->select(function(Select $select) use ($id){
            //$select->columns(array('id','start_hour','end_hour','actual_production','std_production'));
            $select->join(array('sp' => 'SICGA_Standard_Production'), 'SICGA_Production_Model.machine_id = sp.machine_id AND SICGA_Production_Model.sku_size = sp.size', array('machine_runtime'));
            $select->where(array('SICGA_Production_Model.id' => $id));
            $select->order('SICGA_Production_Model.start_hour');
        });

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }

        return $row;
    }

    public function getProductionModelByShiftFilter($shiftFilterArr)
    {
        $resultSet = $this->tableGateway->select(function(Select $select ) use ($shiftFilterArr){
            $select->columns(array('count' => new Expression("COUNT(*)")));
            $select->where(array('DATE(create_date) = ?' => $shiftFilterArr['date_now']));
            $select->where(array('start_hour' => $shiftFilterArr['start_hour']));
            $select->where(array('end_hour' => $shiftFilterArr['end_hour']));
            $select->where(array('machine_id' => $shiftFilterArr['machine_id']));
        });

        return $resultSet;
    }

    public function getDayNowProductionModelsByShiftId($shiftId)
    {
        $machineId = $this->sessionBase->offsetGet('selMachineId');

        $sql = "SELECT
                pm.id, m.name, s.number, p.sku, pm.start_hour, pm.end_hour,
                pm.actual_production, pm.std_production, DATE(pm.create_date)
                FROM  SICGA_Production_Model pm
                JOIN SICGA_Machine m ON (pm.machine_id = m.id)
                JOIN SICGA_Shift s ON (pm.shift_id = s.id)
                JOIN SICGA_Product p ON (pm.product_sku = p.sku)
                WHERE DATE(pm.create_date) =  CURRENT_DATE() AND s.number = $shiftId AND
                pm.machine_id = $machineId
                ORDER BY  pm.end_hour";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function deleteCompleteProductionModel($prodModelId)
    {
        $sql = "DELETE FROM SICGA_Scrap WHERE production_model_id = $prodModelId;
                DELETE FROM SICGA_Production_Dead_Time WHERE production_model_id = $prodModelId;
                DELETE FROM SICGA_Production_Model WHERE id = $prodModelId;
                ";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function getProductionModelsFilterPagination($filter_date,$filter_shift)
    {
        $resultSet = $this->tableGateway->select(function(Select $select) use ($filter_date,$filter_shift){
            $select->columns(array('id','start_hour','end_hour','actual_production','std_production'));
            $select->join(array('m' => 'SICGA_Machine'), 'SICGA_Production_Model.machine_id = m.id', array('name'));
            $select->join(array('s' => 'SICGA_Shift'), 'SICGA_Production_Model.shift_id = s.id', array('number'));
            $select->join(array('p' => 'SICGA_Product'), 'SICGA_Production_Model.product_sku = p.sku', array('sku'));
            $select->where->expression("DATE(SICGA_Production_Model.create_date) = ?", array($filter_date));
            $select->where(array('s.number' => $filter_shift));
            $select->order('SICGA_Production_Model.start_hour');
        });

        return $resultSet;
    }

    public function deleteProductionModel($id)
    {
        $resultSet = $this->tableGateway->delete(array('id' => $id));
        return $resultSet;
    }

    public function saveProductionModel(ProductionModel $productionModel)
    {
        $machineId = $this->sessionBase->offsetGet('selMachineId');
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'machine_id' => $machineId,
            'shift_id' => $productionModel->shift_id,
            'product_sku' => $productionModel->product_sku,
            'production_plan_id' => $productionModel->production_plan_id,
            'start_hour' => $productionModel->start_hour,
            'end_hour' => $productionModel->end_hour,
            'program_time' => $productionModel->program_time,
            'actual_production' => isset($productionModel->actual_production) ? $productionModel->actual_production : 0,
            'std_production' => $productionModel->std_production,
            'sku_size' => $productionModel->sku_size,
            'is_replace' => ($productionModel->is_replace > 0) ? $productionModel->is_replace : 0,
            'is_replace_parent' => ($productionModel->is_replace_parent > 0) ? $productionModel->is_replace_parent : 0,
            'replaced_prod_id' => $productionModel->replaced_prod_id,
        );

        $id = (int)$productionModel->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;

            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getProductionModelById($id))
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
