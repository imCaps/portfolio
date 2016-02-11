<?php namespace Core;

/**
 * Loader for objects
 *
 */
class Loader {

    private static $instance;

    public static function getInstance() {
        if (! self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /*
     * Calling loader by class name
     */
    public function load($name) {
        $loadMethodName = '_load_' . strtolower($name);
        if (method_exists($this, $loadMethodName)) {
            return $this->$loadMethodName();
        } else {
            return null;
        }
    }

    private function _load_app() {
        $app = Registry::get('app');
        if (isset($app)) {
            return $app;
        }
        return false;
    }

    private function _load_logger() {
        return new System\Logger();
    }

    private function _load_db() {
        $config = Registry::get('env');
        return new System\Db($config['db']);
    }
}