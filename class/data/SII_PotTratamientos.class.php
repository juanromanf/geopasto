<?php

class SII_PotTratamientos extends AppActiveRecord {
	public $_table = 'public.p_pot_tratamientos';
	
	public function getNombreTratamiento() {
		return $this->tratamiento;
	}

}
?>