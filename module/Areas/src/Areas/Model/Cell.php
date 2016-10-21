<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 10/14/14
 * Time: 5:20 PM
 */

namespace Areas\Model;


class Cell {
    public $id;
    public $id_sub_area;
    public $name;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->id_sub_area     = (isset($data['id_sub_area'])) ? $data['id_sub_area'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
    }

} 