<?php

class SII_Estratos extends AppActiveRecord {
	public $_table = 'public.p_dem_estrato';
	
	public function getManzanaIGAC() {
		return $this->manzanaigac;
	}
	
	public function getManzanaDANE() {
		return $this->manzanadane;
	}
	
}
?>