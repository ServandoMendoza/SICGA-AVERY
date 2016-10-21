<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 6/10/14
 * Time: 8:05 PM
 */

namespace Scrap\Model;


class ScrapCode{
    public $id;
    public $code;
    public $production_model_id;
    public $description;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    public $machine_id;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->production_model_id = (isset($data['production_model_id '])) ? $data['production_model_id'] : null;
        $this->description  = (isset($data['description'])) ? $data['description'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
        $this->machine_id = (isset($data['machine_id'])) ? $data['machine_id'] : null;
    }

}