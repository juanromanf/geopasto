<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_amenazas
 * 
 * @package data
 *
 */
class SII_PotAmenazas extends AppActiveRecord {
	public $_table = 'public.p_pot_amenazas';
	/**
	 * Retorna el Nombre de Amenaza
	 * estipuladas en el POT para el 
	 * municipio de Pasto
	 * @return String
	 */
	public function getNombreAmenaza() {
		return $this->amenaza;
	}	
}
?>