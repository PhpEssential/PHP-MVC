<?php
namespace framework\sql\core;

use framework\Config;
use framework\exception\SqlException;
use framework\utils\LogUtils;

/**
 * Singleton representant une connexion à la bdd
 */
class DbConnection {
    
    /**
     * Instance du singleton
     * 
     * @var DbConnection
     */
    private static $instance = null;
    
    /**
     * Connexion à la bdd
     * 
     * @var \PDO
     */
	private $connection;
	
	private $activeTransaction = false;
	
	/**
	 * Connexion à la bdd
	 * 
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $dbName
	 */
	private function __construct(string $host, string $user, string $password, string $dbName) {
		$this->connection = new \PDO("mysql:dbname=$dbName;host=$host;charset=UTF8", $user, $password);
	}
	
	/**
	 * Indique si une instance est démarée ou non
	 * 
	 * @return bool
	 */
	public static function hasInstance() : bool {
	    return (self::$instance != null);
	}

	/**
	 * Instancie le singleton si ça n'est pas déjà fait puis retourne ce dernier 
	 * 
	 * @return DbConnection
	 */
	public static function getInstance() : DbConnection {
	    if(self::$instance == null) {
	        self::$instance = new DbConnection(
	            Config::get(Config::DB_HOST), 
	            Config::get(Config::DB_USER), 
	            Config::get(Config::DB_PASSWORD), 
	            Config::get(Config::DB_NAME)
            );
	    }
	    return self::$instance;
	}
	
	public function getLastId(){
		return $this->connection->lastInsertId();
	}
	
	public function hasActiveTransaction() : bool {
		return $this->activeTransaction;
	}
	
	public function startTransaction() {
		if(!$this->connection->beginTransaction()) {
			$errorInfo = $this->connection->errorInfo();
			throw new \PDOException($errorInfo[2], $errorInfo[1]);
		}
		$this->activeTransaction = true;
	}
	
	public function commit() {
		if(!$this->connection->commit()) {
			$errorInfo = $this->connection->errorInfo();
			throw new \PDOException($errorInfo[2], $errorInfo[1]);
		}
		$this->activeTransaction = false;
	}
	
	public function rollback() {
		if(!$this->connection->rollBack()) {
			$errorInfo = $this->connection->errorInfo();
			throw new \PDOException($errorInfo[2], $errorInfo[1]);
		}
		$this->activeTransaction = false;
	}
	
	/**
	 * 
	 * @param string $sqlQuery
	 * 
	 * @return boolean|NULL
	 */
	public function executeScalar(string $sqlQuery) {
		$result = $this->execute($sqlQuery);
		if(count($result) > 0 && count($result[0] > 0)){
			return $result[0][0];
		}
		return null;
	}

	/**
	 * Execute SQL query 
	 * 
	 * @param string $sqlQuery
	 * 
	 * @throws SqlException
	 * @return NULL|array
	 */
	public function execute(string $sqlQuery) {
	    if(defined('DEVELOPMENT')) {
	        echo "<br/>" . $sqlQuery . "<br/>";
	    }
	    
	    $result = array();
	    try {
	    	if(!($statement = $this->connection->prepare($sqlQuery))) {
	    		$errorInfo = $this->connection->errorInfo();
	    		throw new \PDOException($errorInfo[2], $errorInfo[1]);
	    	}
	    	if($statement->execute()) {
	    		while($data = $statement->fetch()) {
	    			$result[] = $data;
	    		}
	    	} else {
	    		$errorInfo = $statement->errorInfo();
	    		throw new \PDOException($errorInfo[2], $errorInfo[1]);
	    	}
	    	$statement = null;
	    } catch (\PDOException $e) {
	    	$e = new SqlException($sqlQuery, $e);
			LogUtils::error($e);
			throw $e;
	    }
		return $result;
	}

	/**
	 * Check if the connection is active
	 * 
	 * @return bool
	 */
	public function isAlive() : bool {
		$result = true;
		try {
			$this->execute('SELECT 1;');
		} catch (SqlException $e) {
			$result = false;
		}
		return $result;
	}
	
	/**
	 * Close the database connection
	 */
	public function closeConnection() {
		$this->connection = null;
	}	
}