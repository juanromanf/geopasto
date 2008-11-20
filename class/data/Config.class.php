<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla configuraciones
 * 
 * @package data
 *
 */
class Config extends AppActiveRecord {
	public $_table = 'app.configuraciones';
	/**
	 * Maneja todas las variables de configuracion
	 * de la herramienta
	 * 	 *
	 * @param Bolean $asJson
	 * @return string JSON | array objetos Config
	 */
	public static function getAllValues($asJson = false) {
		try {
			$obj = new Config();
			$result = $obj->Find ( "key <> 'theme' order by text asc" );
			
			if ($asJson) {
				$root = array ();
				
				foreach ( $result as $c ) {
					$config = array ();
					$config  ['id'] = $c->id_conf;
					$config  ['key'] = $c->key;
					$config  ['text'] = $c->text;
					$config  ['value'] = $c->value;
					
					$root [] = $config;
				}
				return json_encode ( $root );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $result;
	}
	/**
	 * Busca el identificador de la variable
	 *
	 * @param int $key
	 * @return Config
	 */
	public static function getByKey($key) {
		try {
			$obj = new Config();
			$obj->Load ( "key = '$key'" );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $obj;
	}
	/**
	 * Asignar un nuevo item de configuracion
	 *
	 * @param int $key
	 * @param string $value
	 */
	
	public static function setValue($key, $value) {
		try {
			$obj = new Config();
			$obj->Load ( "key = '$key'" );
			$obj->value = trim($value);
			$obj->Update();
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
	}
}
?>