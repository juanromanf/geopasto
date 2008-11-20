<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla perimetro
 * 
 * @package data
 *
 */
class Perimetro extends AppActiveRecord {
	public $_table = 'gis.perimetro';
	/**
	 * Toma las coordenadas del click en la 
	 * que se encuentra para realizar la consulta
	 *
	 * @param int $x
	 * @param int $y
	 * @return array
	 */
	public function getInfoXY($x, $y) {
		
		$info = array ( );
		$info [] = array ('seccion' => 'Perimetro', 'property' => 'Tipo', 'value' => 'Urbano' );
		return $info;
	}

}
?>