<?php namespace Core\Controller;

use Core\Model\Athletes as AthletesModel;
use Core\Registry;
/**
 * User: alexandrshumilow
 * Date: 03/09/15
 * Time: 12:25
 */
class Athletes {

    private $requestBody;
    public $errors;

    public function __construct() {
        $this->requestBody = json_decode(Registry::get('app')->request->getBody());
        $this->errors = array();
    }
    public function processGet() {
        $athletes = AthletesModel::getAthletes();
        if (! empty($athletes)) {
            Registry::get('app')->halt(200, json_encode($athletes));
        }
        $this->errors[] = 'Could not get athletes data';

        Registry::get('app')->halt(400, json_encode($this->errors));
    }
}