<?php
/**
 * 
 * Clase que se encarga de la configuracion de la conexion al SGBD
 * 
 * @package common
 *
 */
class AppSQLSettings {
	private $driver;
	private $host;
	private $user;
	private $password;
	private $database;
	
	/**
	 *	Class constructor 
	 */
	public function __construct($driver, $host, $user, $password, $database) {
		$this->driver = $driver;
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
	}
	
	/**
	 * @return string
	 */
	public function getDatabase() {
		return $this->database;
	}
	
	/**
	 * @return string
	 */
	public function getDriver() {
		return $this->driver;
	}
	
	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}
	
	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}
	
	/**
	 * @return string
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @param string $database
	 */
	public function setDatabase($database) {
		$this->database = $database;
	}
	
	/**
	 * @param string $driver
	 */
	public function setDriver($driver) {
		$this->driver = $driver;
	}
	
	/**
	 * @param string $host
	 */
	public function setHost($host) {
		$this->host = $host;
	}
	
	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
	
	/**
	 * @param string $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}
}

?>
