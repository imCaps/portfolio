<?php namespace Core\System;

/**
 * DB handler class
 *
 * @author Alexandr Shumilow
 */
class DB {

    private $pdoDns;
    private $dbuser;
    private $dbpass;

    private $pdo;

    public function __construct($config) {
        $host = $config['host'];
        if (! $host) {
            $host = 'localhost';
        }
        $this->pdoDns = 'mysql:host=' . $host . ';dbname=' . $config['name'];
        $this->dbuser = $config['user'];
        $this->dbpass = $config['password'];

    }

    /**
     * Connect with DB if isnt connections
     * @param boolean $reconnect
     * @return \PDO
     */
    public function connect($reconnect = false) {
        if (! $reconnect && is_object($this->pdo)) {
            return $this->pdo;
        }

        $attrs = array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        try {
            $this->pdo = null;
            $this->pdo = new \PDO($this->pdoDns, $this->dbuser, $this->dbpass, $attrs);
            return $this->pdo;
        } catch (\PDOException $e) {
            \Core\Registry::get('logger')->api("Could not connect with db $this->pdoDns: " . $e->getMessage(), 2);
            throw new \Exception($e);

        }
    }

    /**
     * Check if exists db connection
     */
    public function isConnect() {
        $pdo = $this->connect();
        if (! $pdo) {
            return false;
        }

        try {
            return (bool) $pdo->query('SELECT 1');
        } catch (\PDOException $e) {}

        return false;
    }

    public function getPdo() {
        $pdo = $this->connect();
        return $pdo;
    }

    /**
     * DO::exec
     * @param string $sql
     * @param array $params
     */
    public function exec($sql, $params = array()) {
        $pdo = $this->connect();
        if (! $pdo) {
            return false;
        }

        if (empty($params)) {
            return $pdo->exec($sql);
        }

        return $this->query($sql, $params);
    }

    /**
     * PDO::query
     * @param string $sql
     * @param array $params
     */
    public function query($sql, $params = array()) {
        $pdo = $this->connect();
        if (! $pdo) {
            return false;
        }

        if (empty($params)) {
            return $pdo->query($sql);
        }

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth;

    }

    /**
     * Db::query, returns all elements in first column
     * @param string $sql
     * @param array $params
     */
    public function queryColumn($sql, $params = array()) {
        $result = $this->query($sql, $params);
        if (! $result) {
            return false;
        }

        return array_map(function($row) {
            return $row[0];
        }, $result->fetchAll());
    }

    /**
     * Db::query, returns first row
     * @param string $sql
     * @param array $params
     */
    public function queryRow($sql, $params = array()) {
        $result = $this->query($sql, $params);
        if (! $result) {
            return false;
        }

        return $result->fetch();
    }
	
	/**
	 * Query All
	 */
	public function queryAll($sql, $params = array()) {
		
		$result = $this->query($sql, $params);
		if (!$result)
			return false;

		return $result->fetchAll();
	}
	
	/**
	 * Last inserted id
	 */
	public function lastInsertId() {
		return (int)$this->queryRow("SELECT LAST_INSERT_ID() as last_id;")['last_id'];
	}
}