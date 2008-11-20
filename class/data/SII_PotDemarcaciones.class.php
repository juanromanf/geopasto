<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_demarcaciones
 * 
 * @package data
 *
 */
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
	 * Retorna el area de Actividad
	 * estipuladas en el POT para el 
	 * municipio de Pasto
	 * 
	 * @return SII_PotAreasActividad
	 */
	public function getAreaActividad() {
		$area = new SII_PotAreasActividad ( );
		$area->Load ( "codareaactividad = '" . $this->codareaactividad . "'" );
		return $area;
	}
	
	/**
	 * Retorna el Tratamiento
	 *
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 * 
	 * @return SII_PotTratamientos
	 */
	public function getTratamiento() {
		$tratamiento = new SII_PotTratamientos ( );
		$tratamiento->Load ( "codtratamiento = '" . $this->codtratamiento . "'" );
		return $tratamiento;
	}
	
	/**
	 * Retorna el nombre del Subsector
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 * 
	 * @return SII_PotSubSectores
	 */
	public function getSubSector() {
		$subsector = new SII_PotSubSectores ( );
		$subsector->Load ( "codsubsector = '" . $this->codsubsector . "'" );
		return $subsector;
	}
	/**
	 * Retorna el indice de ocupacion
	 *
	 * @return unknown
	 */
	public function getIocupacion() {
		return $this->iocupacion;
	}
	/**
	 * Retorna el indice de construccion
	 *
	 * @return unknown
	 */
	public function getIconstruccion() {
		return $this->iconstruccion;
	}
	/**
	 * Retorna el indice de Cesion
	 *
	 * @return unknown
	 */	
	public function getIcesion() {
		return $this->icesion;
	}
	
	/**
	 * Retorna la tipologia de la via
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 * @return SII_PotTipologiaVias
	 */
	public function getTipologia() {
		$tipologia = new SII_PotTipologiaVias ( );
		$tipologia->Load ( "codtipologia = '" . $this->codtipologiavia . "'" );
		return $tipologia;	
	}
	/**
	 * Retorna la distancia del eje
	 *
	 * @return SII_PotDemarcaciones
	 */
	public function getDistanciaEje() {
		return $this->eje;
	}

}
?>