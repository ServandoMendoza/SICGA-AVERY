<?php
namespace ProductionModel\Model;

class NoProgram
{
    public $id;
    public $id_machine;
    public $is_active;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->id_machine     = (isset($data['id_machine'])) ? $data['id_machine'] : null;
        $this->is_active     = (isset($data['is_active'])) ? $data['is_active'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date  = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by  = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
    }
}
