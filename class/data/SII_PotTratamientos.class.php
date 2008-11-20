<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_tratamiento
 * 
 * @package data
 *
 */
class SII_PotTratamientos extends AppActiveRecord {
	public $_table = 'public.p_pot_tratamientos';
	/**
	 * Retorna el nombre del tratamiento
	 * estipulad@s en el POT para el 
	 * municipio de Pasto
	 *
	 * @return String
	 */
	public function getNombreTratamiento() {
		return $this->tratamiento;
	}

}
?>