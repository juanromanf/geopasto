<?php

class SII_EstadoLotes extends AppActiveRecord {
	public $_table = 'public.p_dem_estadoslotes';
	
	public function getEstado() {
		return $this->estado;
	}
}
?>