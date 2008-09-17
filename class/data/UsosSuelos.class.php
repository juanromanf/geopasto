<?php

class UsosSuelos extends AppActiveRecord {
	public $_table = 'gis.usos_suelo';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('gid', 'numpredio');
		parent::__construct ( $xajaxResponse, FALSE, $keys);
	}
}
?>