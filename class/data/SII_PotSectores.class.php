<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_sectores
 * 
 * @package data
 *
 */
class SII_PotSectores extends AppActiveRecord {
	public $_table = 'public.p_pot_sectores';
	/**
	 * Retorna el nombre del sector
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 *
	 * @return String
	 */
	public function getNombreSector() {
		return $this->sector;
	}
	
	/**
	 * Retorna el nombre de la pieza urbana
	 * estipuladas en el POT para el 
	 * municipio de Pasto
	 * @return SII_PotPiezasUrbanas
	 */
	public function getPiezaUrbana() {
		$pieza = new SII_PotPiezasUrbanas ( );
		$pieza->Load ( "codpiezasurbana = '" . $this->codpiezasurbana . "'" );
		return $pieza;
	}
}
?>