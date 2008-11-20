<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_dem_estadoslotes
 * 
 * @package data
 *
 */

class SII_EstadoLotes extends AppActiveRecord {
	public $_table = 'public.p_dem_estadoslotes';
	/**
	 * Toma el estado del lote
	 *
	 * @return String
	 */
	public function getEstado() {
		return $this->estado;
	}
}
?>