<?php

class SII_PotPiezasUrbanas extends AppActiveRecord {
	public $_table = 'public.p_pot_piezasurbanas';
	
	public function getNombrePieza() {
		return $this->piezasurbana;
	}
}
?>