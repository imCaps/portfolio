<?php namespace Core;

/**
 * Class for objects (Registry pattern)
 */
class Registry {

    const AUTO_LOADING = true;

    private static $instance = null;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Registry();
        }

        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}

    private static $storage = array();


    public static function set($key, $value) {
        $key = strtolower($key);
        self::$storage[$key] = $value;
    }

    public static function get($key) {
        $key = strtolower($key);
        $storage = self::$storage;

        if (isset($storage[$key])) {
            return $storage[$key];
        }

        // If object is missing, create it
        if (self::AUTO_LOADING) {
            $object = Loader::getInstance()->load($key);
            if ($object !== null) {
                self::set($key, $object);
                return $object;
            }
        }

        return null;
    }

    public static function remove($key) {
        unset(self::$storage[$key]);
    }
}

