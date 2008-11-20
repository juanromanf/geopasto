<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_clasesuelos
 * 
 * @package data
 *
 */
class SII_PotClaseSuelos extends AppActiveRecord {
	public $_table = 'public.p_pot_clasesuelos';
	/**
	 * Retorna el nombre de la clase de suelo
	 * estipulados en el POT para el 
	 * municipio de Pasto
	 * @return String
	 */
	public function getNombreSuelo() {
		return $this->clasesuelo;
	}
}
?>