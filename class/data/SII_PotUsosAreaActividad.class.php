<?php

class SII_PotUsosAreaActividad extends AppActiveRecord {
	public $_table = 'public.p_pot_usosareaactividad';
	
	public function getSigla() {
		return $this->sigla;
	}
	
	public function getTipoUso() {
		$tipo = ($this->tipousu == "C") ? 'Condicionado' : 'Principales';
		return $tipo;
	}

	/**
	 * Enter description here...
	 *
	 * @return SII_PotImpactoNaturaleza
	 */
	public function getImpacto() {
		$obj = new SII_PotImpactoNaturaleza();
		$obj->Load("sigla = '". $this->getSigla() . "'");
		
		return $obj;
	}
	
}
?>