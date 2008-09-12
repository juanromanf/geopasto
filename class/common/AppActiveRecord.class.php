<?php

class AppActiveRecord extends ADODB_Active_Record {
	/**
	 * Objeto de manipulacion de AJAX.
	 *
	 * @var xajaxResponse
	 */
	private $xResponse = NULL;
	
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
}
?>