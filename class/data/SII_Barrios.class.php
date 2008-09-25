<?php

class SII_Barrios extends AppActiveRecord {
	public $_table = 'public.p_div_barrios';
	
	public function getNombreBarrio() {
		return $this->barrio;
	}
	
	public function getComuna() {
		return $this->codcomuna;
	}
}
?>