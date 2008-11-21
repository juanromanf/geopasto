<?php
/**
 * 
 * Clase responsable de proveer la capa de abstraccion de bases de datos,
 * mediante el framework ADODB.
 * 
 * @package common
 *
 */

class AppSQL {
	
	public function __construct() {
	}
	
	/**
	 * Retorna una instancia de la conexion a la base de datos de acuerdo al
	 * perfil definido. Soporta multiples conexiones.
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
				$settings = new AppSQLSettings ( 'postgres', 'localhost', 'postgres', 'postgres', 'geopasto' );
				break;
		}
		
		try {
			/* @var db ADOConnection */
			$db = & ADONewConnection ( $settings->getDriver () );
			ADOdb_Active_Record::SetDatabaseAdapter ( $db );
			
			$db->Connect ( $settings->getHost (), $settings->getUser (), $settings->getPassword (), $settings->getDatabase () );
		
		} catch ( Exception $e ) {
			var_dump ( $e );
			adodb_backtrace ( $e->getTrace () );
		}
		return $db;
	}
}
?>