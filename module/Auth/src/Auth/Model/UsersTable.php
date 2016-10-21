<?php
namespace Auth\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

use Custom\DataTables;

class UsersTable
{
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

    public function getUser($usr_id)
    {
        $usr_id  = (int) $usr_id;
        $rowset = $this->tableGateway->select(array('usr_id' => $usr_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $usr_id");
        }
        return $row;
    }

    public function getTechsByShift($id_shift)
    {
        $sql = "SELECT u.usr_name as tech_name, r.number as requisition_number, m.name as machine_name,
                IF (r.number is null, 'Disponible' , 'Ocupado') as status, w.type as wo_activity, w.number as wo_number,
                photo
                FROM users u
                LEFT JOIN (SELECT max(number)as number,id_area,id_tech
                    FROM SICGA_Requisition r WHERE free = 0 GROUP BY id_tech)
                    r ON (r.id_tech = u.usr_id)
                left JOIN SICGA_Machine m on (r.id_area = m.id)
                    LEFT JOIN (SELECT max(number)as number,type,id_tech
                    FROM SICGA_Tech_Work w WHERE free = 0 GROUP BY id_tech)
                    w ON (w.id_tech = u.usr_id)
                WHERE u.usrl_id = 3 AND u.id_shift = $id_shift";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function isTechBusy($id_teach)
    {
        // Cuenta las actividades que tiene, si no, regresa 0
        $sql = "SELECT COUNT(1) AS count
                FROM (
                    SELECT id_tech, free FROM SICGA_Requisition
                    UNION ALL
                    SELECT id_tech, free FROM SICGA_Tech_Work
                ) a
                WHERE id_tech = $id_teach AND free = 0";

        $stmt = $this->adapter->query($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function getTechUser()
    {
        $rowset = $this->tableGateway->select(array('usrl_id' => 3));

        if (!$rowset) {
            throw new \Exception("Could not find tech users");
        }
        return $rowset;

    }

    public function isValidUserToken($token_bypass)
    {
        $token_bypass = (int)$token_bypass;

        $rowset = $this->tableGateway->select(array('bypass_token' => $token_bypass));
        $row = $rowset->current();

        return $row;
    }

	public function getUserByToken($token)
    {
        $rowset = $this->tableGateway->select(array('usr_registration_token' => $token));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $token");
        }
        return $row;
    }
	
    public function activateUser($usr_id)
    {
		$data['usr_active'] = 1;
		$data['usr_email_confirmed'] = 1;
		$this->tableGateway->update($data, array('usr_id' => (int)$usr_id));
    }	

    public function getUserByEmail($usr_email)
    {
        $rowset = $this->tableGateway->select(array('usr_email' => $usr_email));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $usr_email");
        }
        return $row;
    }

    public function getTechByShift($id_shift)
    {
        $rowset = $this->tableGateway->select(array('is_active' => 1, 'usrl_id' => 3, 'id_shift' => $id_shift));
        //$row = $rowset->current();
        if (!$rowset) {
            throw new \Exception("Could not find row with shift $id_shift");
        }
        return $rowset;
    }

    public function changePassword($usr_id, $password)
    {
		$data['password'] = $password;
		$this->tableGateway->update($data, array('usr_id' => (int)$usr_id));
    }

    public function getListJson($request, $sql_details, $table, $primaryKey, $columns )
    {
        $bindings = array();

        // Build the SQL query string from the request
        $limit = DataTables::limit( $request, $columns );
        $order = DataTables::order( $request, $columns );
        $where = DataTables::filter( $request, $columns, $bindings);

        $sql_raw = "SELECT SQL_CALC_FOUND_ROWS *
			 FROM $table
			 $where
			 $order
			 $limit";

        $return = DataTables::simple($request, $sql_details, $table, $primaryKey, $columns, $bindings, $sql_raw );
        return $return;
    }
	
    public function saveUser(Auth $auth)
    {
		// for Zend\Db\TableGateway\TableGateway we need the data in array not object
        $data = array(
            'usr_full_name' 	    => $auth->usr_full_name,
            'usr_name' 				=> $auth->usr_name,
            'usr_password'  		=> $auth->usr_password,
            'usr_email'  			=> $auth->usr_email,
            'usrl_id'  				=> $auth->usrl_id,
            'lng_id'  				=> $auth->lng_id,
            'usr_active'  			=> empty($auth->usr_active) ? 1: $auth->usr_active,
            'usr_question'  		=> $auth->usr_question,
            'usr_answer'  			=> $auth->usr_answer,
            'usr_picture'  			=> $auth->usr_picture,
            'usr_password_salt' 	=> $auth->usr_password_salt,
            'usr_registration_date' => $auth->usr_registration_date,
            'usr_registration_token'=> $auth->usr_registration_token,
			'usr_email_confirmed'	=> empty($auth->usr_email_confirmed) ? 1 : $auth->usr_email_confirmed,
            'usrl_id'	=> $auth->usrl_id,
            'id_shift'	=> $auth->id_shift,
            'raw_password'	=> $auth->raw_password,
            'bypass_token'	=> $auth->bypass_token,
            'photo'	=> $auth->photo,
        );
		// If there is a method getArrayCopy() defined in Auth you can simply call it.
		// $data = $auth->getArrayCopy();

        $usr_id = (int)$auth->usr_id;
        if ($usr_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUser($usr_id)) {
                $this->tableGateway->update($data, array('usr_id' => $usr_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
	
    public function deleteUser($usr_id)
    {
        //$this->tableGateway->delete(array('usr_id' => $id));
        if ($this->getUser($usr_id)) {
            $this->tableGateway->update(array('is_active' => 0), array('usr_id' => $usr_id));
        }
        else {
            throw new \Exception('User id does not exist');
        }
    }	
}