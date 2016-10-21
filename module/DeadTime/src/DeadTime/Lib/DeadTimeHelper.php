<?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 11/24/14
 * Time: 8:53 PM
 */
namespace DeadTime\Lib;

class DeadTimeHelper {
    static function willCreateRequisition($death_code_id, $codes_for_req)
    {
        $retVal = false;

        foreach($codes_for_req as $dead_code) {
            if($dead_code["dead_code_id"] == $death_code_id) {
                $retVal = true;
                break;
            }
        }

        return $retVal;
    }
} 