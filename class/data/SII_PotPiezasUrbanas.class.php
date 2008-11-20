<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_piezasurbanas
 * 
 * @package data
 *
 */
class SII_PotPiezasUrbanas extends AppActiveRecord {
	public $_table = 'public.p_pot_piezasurbanas';
	/**
	 * Retorna el nombre de la pieza urbana
	 * estipulad@s en el POT para el 
	 * municipio de Pasto
	 * @return String
	 */
	public function getNombrePieza() {
		return $this->piezasurbana;
	}
}
?>