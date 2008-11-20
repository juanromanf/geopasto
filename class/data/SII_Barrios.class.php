<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_div_barrios
 * 
 * @package data
 *
 */
class SII_Barrios extends AppActiveRecord {
	public $_table = 'public.p_div_barrios';
	/**
	 * Retorna el nombre del barrio
	 *
	 * @return string
	 */
	public function getNombreBarrio() {
		return $this->barrio;
	}
	/**
	 * Retorna el codigo de la comuna
	 *
	 * @return String
	 */
	public function getComuna() {
		return $this->codcomuna;
	}
}
?>