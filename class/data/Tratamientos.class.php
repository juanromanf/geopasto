<?php

class Tratamientos extends AppActiveRecord {
	public $_table = 'gis.tratamientos';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('oid');
		parent::__construct ( $xajaxResponse, FALSE, $keys);
	}
}
?>