<?php namespace Core\System;

/**
 * Logger handler class
 *
 * @author Alexandr Shumilow
 */

class Logger {

    public $connectionUID = '';
    private $logsDir = 'logs';

    private $apiLogger;

    public function __construct() {
        $this->connectionUID = uniqid();

        $this->logsDir = __DIR__ . '/../' . $this->logsDir;
        if (! is_dir($this->logsDir)) {
            mkdir($this->logsDir, 0777, true);
        }

        $this->apiLogger = new \Monolog\Logger('api');
        $this->apiLogger->pushHandler(new \Monolog\Handler\RotatingFileHandler($this->logsDir . '/api.log'));
    }


    /**
     * Write to api log
     * @param array $msg
     * @param int $lvl - 0(info), 1(warning), 2(error)
     */
    public function api($msg, $lvl = 0) {
        $msg = $this->connectionUID . ' - ' . $msg;
        switch($lvl) {
            case 1:
                $this->apiLogger->addWarning($msg);
                break;
            case 2:
                $this->apiLogger->addError($msg);
                break;
            default:
                $this->apiLogger->addInfo($msg);
                break;
        }
    }


}