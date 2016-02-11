<?php namespace Core\Model;

/**
 * User: alexandrshumilow
 * Date: 03/09/15
 * Time: 12:25
 */
class Athletes {

    public function __construct() {

    }

    public static function getAthletes() {
        return \Core\Registry::get('db')
            ->queryAll("SELECT start_number, full_name, chip_id FROM athletes ORDER BY start_number");
    }
}