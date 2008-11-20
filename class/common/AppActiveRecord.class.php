<?php
/**
 * 
 * Es una clase que implementa el patron de 
 * ADOB_Active_Record para el acceso de Datos
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
	 * Convertir paramatros en un Array
	 *
	 * @return json
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