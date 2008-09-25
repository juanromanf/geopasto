<?php

class SII_PotSectores extends AppActiveRecord {
	public $_table = 'public.p_pot_sectores';
	
	public function getNombreSector() {
		return $this->sector;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotPiezasUrbanas
	 */
	public function getPiezaUrbana() {
		$pieza = new SII_PotPiezasUrbanas ( );
		$pieza->Load ( "codpiezasurbana = '" . $this->codpiezasurbana . "'" );
		return $pieza;
	}
}
?>