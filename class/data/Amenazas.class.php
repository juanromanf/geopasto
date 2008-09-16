<?php

class Amenazas extends AppActiveRecord {
	public $_table = 'gis.amenazas';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('oid', 'numpredio');
		parent::__construct ( $xajaxResponse, FALSE, $keys);
	}
}
?>