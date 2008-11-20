<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla gis
 * 
 * @package data
 *
 */
class Comunas extends AppActiveRecord {
	public $_table = 'gis.comunas';
	/**
	 * Toma las coordenadas del click en la 
	 * que se encuentra para realizar la consulta
	 *
	 * @param int $x
	 * @param int $y
	 * @return array
	 */
	public function getInfoXY($x, $y) {
		$toleracia = 20;
		$x1 = $x - $toleracia;
		$y1 = $y - $toleracia;
		$x2 = $x + $toleracia;
		$y2 = $y + $toleracia;
		
		// Spatial SQL para layers tipo Polygon
		$query = "SELECT num_comuna FROM gis.comunas t
						 WHERE the_geom && 'BOX3D($x1 $y1, $x2 $y2)'::box3d AND
						 Intersects(GeometryFromText( 'POINT($x $y)', -1), t.the_geom)";
		
		$db = AppSQL::getInstance ();
		$rs = $db->Execute ( $query );
		$num_comuna = $rs->fields [0];
		$info = array ();
		$info [] = array ('seccion' => 'Comunas', 'property' => 'Comuna', 'value' => $num_comuna );
		return $info;
	}

}
?>