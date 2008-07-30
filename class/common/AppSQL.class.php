<?php

class AppSQL {

	public function __construct() { }

	/**
	 * Connections Factory method, get a instance of database connection profile. 
	 *
	 * @return ADOConnection
	 */
	public static function getInstance($connection_name = 'default') {	
		
		/**
		 * Add connections here.
		 */
		$settings = NULL;
		
		switch ($connection_name) {
			case 'default' :
				$settings = new AppSQLSettings('postgres', 'localhost', 'postgres', 'postgres', 'geopasto');
				break;
		}
		
		try {		
			/* @var db ADOConnection */
			$db =& ADONewConnection( $settings->getDriver() );
			ADOdb_Active_Record::SetDatabaseAdapter($db);
			
			$db->Connect($settings->getHost(), $settings->getUser(), $settings->getPassword(), $settings->getDatabase());

			return $db;

		} catch (Exception $e) {
			var_dump($e);
			adodb_backtrace($e->getTrace());
			return NULL;
		}
	}
}
?>