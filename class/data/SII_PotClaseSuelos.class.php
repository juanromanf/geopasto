<?php

class SII_PotClaseSuelos extends AppActiveRecord {
	public $_table = 'public.p_pot_clasesuelos';
	
	public function getNombreSuelo() {
		return $this->clasesuelo;
	}
}
?>