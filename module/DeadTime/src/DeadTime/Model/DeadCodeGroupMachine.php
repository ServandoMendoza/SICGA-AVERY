<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 10/3/14
 * Time: 8:35 PM
 */

namespace DeadTime\Model;


class DeadCodeGroupMachine {
    public $death_code_group_id;
    public $machine_id;

    public function exchangeArray($data)
    {
        $this->machine_id    = (isset($data['machine_id'])) ? $data['machine_id'] : null;
        $this->death_code_group_id = (isset($data['death_code_group_id'])) ? $data['death_code_group_id'] : null;
    }

} 