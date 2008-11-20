<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_dem_estrato
 * 
 * @package data
 *
 */
class SII_Estratos extends AppActiveRecord {
	public $_table = 'public.p_dem_estrato';
	/**
	 * Retorna el numero de la manzana IGAC
	 *
	 * @return String
	 */
	public function getManzanaIGAC() {
		return $this->manzanaigac;
	}
	/**
	 * Retorna el numero de la manzana DANE
	 *
	 * @return String
	 */
	public function getManzanaDANE() {
		return $this->manzanadane;
	}
	
}
?>