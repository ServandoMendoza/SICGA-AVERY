<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 10/14/14
 * Time: 5:20 PM
 */

namespace Areas\Model;


class CellMachine {
    public $id;
    public $id_cell;
    public $id_machine;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->id_cell = (isset($data['id_cell'])) ? $data['id_cell'] : null;
        $this->id_machine = (isset($data['id_machine'])) ? $data['id_machine'] : null;

    }

} 