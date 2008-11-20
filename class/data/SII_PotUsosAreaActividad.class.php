<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_usosareaactividad
 * 
 * @package data
 *
 */
class SII_PotUsosAreaActividad extends AppActiveRecord {
	public $_table = 'public.p_pot_usosareaactividad';
	/**
	 * Retorna la sigla del area de actividad
	 * estipulad@s en el POT para el 
	 * municipio de Pasto
	 *
	 * @return String
	 */
	public function getSigla() {
		return $this->sigla;
	}
	/**
	 * Retorna los tipos de uso de suelos para el predio
	 * estipulad@s en el POT para el 
	 * municipio de Pasto
	 *
	 * @return array SII_PotUsosAreaActividad
	 */
	public function getTipoUso() {
		$tipo = ($this->tipousu == "C") ? 'Condicionado' : 'Principales';
		return $tipo;
	}
	
	/**
	 * Retorna la sigla del impacto 
	 * estipulad@s en el POT para el 
	 * municipio de Pasto
	 *
	 * @return SII_PotImpactoNaturaleza
	 */
	public function getImpacto() {
		$obj = new SII_PotImpactoNaturaleza ( );
		$obj->Load ( "sigla = '" . $this->getSigla () . "'" );
		
		return $obj;
	}

}
?>