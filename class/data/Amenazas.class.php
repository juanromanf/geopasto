<?php

class Amenazas extends AppActiveRecord {
	public $_table = 'gis.amenazas';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('oid');
		parent::__construct ( $xajaxResponse, FALSE, $keys);
	}
}
?>