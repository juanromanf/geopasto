<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_subsectores
 * 
 * @package data
 *
 */
class SII_PotSubSectores extends AppActiveRecord {
	public $_table = 'public.p_pot_subsectores';
	/**
	 * Retorna el nombre del subsector
	 * estipuladas en el POT para el 
	 * municipio de Pasto
	 * @return String
	 */
	public function getNombreSubsector() {
		return $this->subsector;
	}
	
	/**
	 * Retorna el Sector
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 * @return SII_PotSectores
	 */
	public function getSector() {
		$sector = new SII_PotSectores ( );
		$sector->Load ( "codsector = '" . $this->codsector . "'" );
		return $sector;
	}
}
?>