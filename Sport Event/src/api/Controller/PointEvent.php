<?php namespace Core\Controller;

use Core\Model\PointEvent as PointEventModel;
use Core\Registry;

/**
 * Point events controller
 * @author Alexandr Shumilow
 */
class PointEvent {

    private $requestBody;
    public $errors;

    /**
     * Constructor
     */
    public function __construct() {
        $this->requestBody = json_decode(Registry::get('app')->request->getBody());
        $this->errors = array();
    }

    /**
     * Processing post request from client.
     */
    public function processPost() {
        if ($this->validateRequestBody()) {
            if (PointEventModel::registerEvent($this->requestBody->athleteChipId, $this->requestBody->pointId)) {
                Registry::get('app')->halt(200);
            }
            $this->errors[] = 'Could not register event';
        }
        Registry::get('app')->halt(400, json_encode($this->errors));
    }

    /**
     * Processing get request from client.
     */
    public function processGet() {
        $events = PointEventModel::getEvents();
        if (isset($events)) {
            Registry::get('app')->halt(200, json_encode($events));
        }
        $this->errors[] = 'Could not get events';

        Registry::get('app')->halt(400, json_encode($this->errors));
    }

    /**
     * Validating request body from client.
     * @return bool
     */
    private function validateRequestBody() {
        if (isset($this->requestBody->athleteChipId)
            && isset($this->requestBody->pointId)
        ) {
            if (! PointEventModel::checkAthlete($this->requestBody->athleteChipId)
            ) {
                $this->errors[] = 'Athlete is not registered';
                return false;
            }
            if (! PointEventModel::checkPoint($this->requestBody->pointId)) {
                $this->errors[] = 'Point not exists';
                return false;
            }
            return true;
        }

        $this->errors[] = 'Missing params';
        return false;
    }
}