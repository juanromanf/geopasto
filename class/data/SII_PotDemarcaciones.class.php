<?php

class SII_PotDemarcaciones extends AppActiveRecord {
	public $_table = 'public.p_pot_demarcaciones';
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotClaseSuelos
	 */
	public function getClaseSuelo() {
		$suelo = new SII_PotClaseSuelos ( );
		$suelo->Load ( "codclasesuelo = '" . $this->codclasesuelo . "'" );
		return $suelo;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotAreasActividad
	 */
	public function getAreaActividad() {
		$area = new SII_PotAreasActividad ( );
		$area->Load ( "codareaactividad = '" . $this->codareaactividad . "'" );
		return $area;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotTratamientos
	 */
	public function getTratamiento() {
		$tratamiento = new SII_PotTratamientos ( );
		$tratamiento->Load ( "codtratamiento = '" . $this->codtratamiento . "'" );
		return $tratamiento;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotSubSectores
	 */
	public function getSubSector() {
		$subsector = new SII_PotSubSectores ( );
		$subsector->Load ( "codsubsector = '" . $this->codsubsector . "'" );
		return $subsector;
	}
	
	public function getIocupacion() {
		return $this->iocupacion;
	}
	
	public function getIconstruccion() {
		return $this->iconstruccion;
	}
	
	public function getIcesion() {
		return $this->icesion;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotTipologiaVias
	 */
	public function getTipologia() {
		$tipologia = new SII_PotTipologiaVias ( );
		$tipologia->Load ( "codtipologia = '" . $this->codtipologiavia . "'" );
		return $tipologia;	
	}
	
	public function getDistanciaEje() {
		return $this->eje;
	}

}
?>