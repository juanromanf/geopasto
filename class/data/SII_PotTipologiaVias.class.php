<?php

class SII_PotTipologiaVias extends AppActiveRecord {
	public $_table = 'public.p_pot_tipologiavias';
	
	public function getNombreTipologia() {
		return $this->tipologia;
	}
}
?>