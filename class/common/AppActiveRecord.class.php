<?php
/**
 * 
 * Clase que implementa el patron
 * Active Record para el manejo a bases de datos.
 * 
 * @package common
 */
class AppActiveRecord extends ADODB_Active_Record {
	/**
	 * Objeto de manipulacion de AJAX.
	 *
	 * @var xajaxResponse
	 */
	private $xResponse = NULL;
	/**
	 * Constructor de la conexion
	 *
	 * @param xajaxResponse $xajaxResponse
	 * @param table $myTable
	 * @param primaryKey $myPkeys
	 * @param conexion $myCon
	 */
	public function __construct($xajaxResponse = false, $myTable = false, $myPkeys = false, $myCon = false) {
		
		$myCon = $myCon ? $myCon : AppSQL::getInstance ();
		
		parent::__construct ( $myTable, $myPkeys, $myCon );
		$this->xResponse = $xajaxResponse;
	}
	
	/**
	 * Getter para propiedad xResponse.
	 *
	 * @return xajaxResponse
	 */
	public function getXajaxResponse() {
		return $this->xResponse;
	}
	
	/**
	 * Construye un array asociativo con los nombres de los atributos de una tupla
	 * y sus respectivos valores.
	 *
	 * @return array 
	 */
	public function toArray() {
		$fields = $this->GetAttributeNames ();
		$json = array ( );
		foreach ( $fields as $field ) {
			$json [$field] = $this->$field;
		}
		return $json;
	}
}
?>