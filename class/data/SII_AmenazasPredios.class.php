<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_amenaza
 * 
 * @package data
 *
 */
class SII_AmenazasPredios extends AppActiveRecord {
	public $_table = 'public.p_amenaza';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('numpredio', 'codamenaza' );
		parent::__construct ( $xajaxResponse, FALSE, $keys );
	}

}
?>