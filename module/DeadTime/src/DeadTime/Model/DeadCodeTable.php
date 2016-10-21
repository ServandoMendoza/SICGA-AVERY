<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:09 PM
 */

namespace DeadTime\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Session\Container as SessionContainer;
use Custom\DataTables;

class DeadCodeTable {

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

    public function fetchAllById($id)
    {
        $resultSet = $this->tableGateway->select(array('id' => $id));

        $row = $resultSet->current();
        if (!$row) {
            $row = null;
        }
        return $row;
    }

    public function getDeadCodesIdForRequisition()
    {
        $sql = "SELECT dc.id as dead_code_id FROM SICGA_Death_Code dc
                JOIN SICGA_Death_Code_Group dcg ON (dc.death_code_group_id = dcg.id)
                WHERE dc.code in (122,134) or dcg.id in (45,11);";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }


    public function deadCodeExists($code)
    {
        $resultSet = $this->tableGateway->select(array('code' => $code));
        $row = $resultSet->count();

        return ($row > 0);

    }

    public function fetchAllByDeadCodeGroup($dead_code_group_id)
    {
        $dead_code_group_id = (int)$dead_code_group_id;
        $resultSet = $this->tableGateway->select(array('death_code_group_id' => $dead_code_group_id, 'is_active' => 1));
        return $resultSet;
    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings);

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS * FROM $table
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }

    public function deleteDeadCode($dc_id)
    {
        //$this->tableGateway->delete(array('usr_id' => $id));
        if ($this->fetchAllById($dc_id)) {
            $this->tableGateway->update(array('is_active' => 0), array('id' => $dc_id));
        }
        else {
            throw new \Exception('Dead Code id does not exist');
        }
    }

    public function saveDeadCode(DeadCode $deadCode)
    {
        $usrId = $this->sessionBase->offsetGet('idUser');

        $data = array(
            'code' => $deadCode->code,
            'death_code_group_id' => $deadCode->death_code_group_id,
            'description' => $deadCode->description
        );

        $id = (int)$deadCode->id;

        if ($id == 0)
        {
            $data["create_by"] = $usrId;
            $this->tableGateway->insert($data);

            return $this->tableGateway->lastInsertValue;
        }
        else
        {
            if ($this->fetchAllById($id))
            {
                $data['update_date'] =  date('Y-m-d H:i:s');
                $data["update_by"] = $usrId;

                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Dead Code id does not exist');
            }
        }
    }

} 