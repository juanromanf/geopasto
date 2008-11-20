<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla tratamientos
 * 
 * @package data
 *
 */
class Tratamientos extends AppActiveRecord {
	public $_table = 'gis.tratamientos';
	/**
	 * Constructor de la clase
	 *
	 * @param xajaxREsponse $xajaxResponse
	 */
	public function __construct($xajaxResponse = false) {
		$keys = array ('oid');
		parent::__construct ( $xajaxResponse, FALSE, $keys);
	}
}
?>