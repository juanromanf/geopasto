<?php

class SII_PotAreasActividad extends AppActiveRecord {
	public $_table = 'public.p_pot_areasactividad';
	
	public function getNombreArea() {
		return $this->areaactividad;
	}
}
?>