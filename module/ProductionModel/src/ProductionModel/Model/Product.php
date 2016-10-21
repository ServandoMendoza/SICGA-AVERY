<?php
namespace ProductionModel\Model;

class Product
{
    public $sku;
    public $sku_prefix;
    public $name;
    public $description;
    public $size;
    public $create_date;
    public $update_date;
    public $create_by;
    public $update_by;
    
    public function exchangeArray($data)
    {
        $this->sku     = (isset($data['sku'])) ? $data['sku'] : null;
        $this->sku_prefix     = (isset($data['sku_prefix'])) ? $data['sku_prefix'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->description  = (isset($data['description'])) ? $data['description'] : null;
        $this->size = (isset($data['size'])) ? $data['size'] : null;
        $this->create_date = (isset($data['create_date'])) ? $data['create_date'] : null;
        $this->update_date = (isset($data['update_date'])) ? $data['update_date'] : null;
        $this->create_by = (isset($data['create_by'])) ? $data['create_by'] : null;
        $this->update_by = (isset($data['update_by'])) ? $data['update_by'] : null;
    }
}
