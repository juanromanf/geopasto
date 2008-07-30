<?php

class AppActiveRecord extends ADODB_Active_Record {
	/**
	 * Objeto de manipulacion de AJAX.
	 *
	 * @var xajaxResponse
	 */
	private $xResponse = NULL;
	
	public function __construct($xajaxResponse = NULL) {
		
		parent::__construct ( FALSE, FALSE, AppSQL::getInstance () );
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