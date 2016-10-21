<?php

namespace DeadTime\Model;

class DeadProblem {
    public $id;
    public $death_section_id;
    public $code;
    public $description;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->death_section_id = (isset($data['death_section_id'])) ? $data['death_section_id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->description  = (isset($data['description'])) ? $data['description'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
    }
} 