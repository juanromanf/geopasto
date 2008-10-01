<?php

class SII_PotAmenazas extends AppActiveRecord {
	public $_table = 'public.p_pot_amenazas';
	
	public function getNombreAmenaza() {
		return $this->amenaza;
	}	
}
?>