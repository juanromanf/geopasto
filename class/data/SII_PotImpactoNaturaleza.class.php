<?php

class SII_PotImpactoNaturaleza extends AppActiveRecord {
	public $_table = 'public.p_pot_impactonaturaleza';
	
	public function getDescripcion() {
		return $this->descripcion;
	}	
}
?>