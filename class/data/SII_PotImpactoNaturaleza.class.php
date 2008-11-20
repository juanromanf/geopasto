<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_impactonaturaleza
 * 
 * @package data
 *
 */
class SII_PotImpactoNaturaleza extends AppActiveRecord {
	public $_table = 'public.p_pot_impactonaturaleza';
	/**
	 * Retorna la descripcion del impacto
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 * @return String
	 */
	public function getDescripcion() {
		return $this->descripcion;
	}	
}
?>