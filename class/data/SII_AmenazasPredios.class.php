<?php

class SII_AmenazasPredios extends AppActiveRecord {
	public $_table = 'public.p_amenaza';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('numpredio', 'codamenaza' );
		parent::__construct ( $xajaxResponse, FALSE, $keys );
	}

}
?>