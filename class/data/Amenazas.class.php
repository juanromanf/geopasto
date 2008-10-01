<?php

class Amenazas extends AppActiveRecord {
	public $_table = 'gis.amenazas';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('oid' );
		parent::__construct ( $xajaxResponse, FALSE, $keys );
	}
	
	public function getInfoXY($x, $y) {
		$toleracia = 20;
		$x1 = $x - $toleracia;
		$y1 = $y - $toleracia;
		$x2 = $x + $toleracia;
		$y2 = $y + $toleracia;
		
		$info = array ();
		// Spatial SQL para layers tipo Polygon
		$query = "SELECT numpredio FROM gis.amenazas t
						 WHERE the_geom && 'BOX3D($x1 $y1, $x2 $y2)'::box3d AND
						 Intersects(GeometryFromText( 'POINT($x $y)', -1), t.the_geom)";
		
		$db = AppSQL::getInstance ();
		$rs = $db->Execute ( $query );
		$numpredio = $rs->fields [0];
		
		if ($numpredio) {
			$predio = new SII_Predios ( );
			$predio->Load ( "numpredio = '$numpredio'" );
			$this->Load ( "numpredio = '$numpredio'" );
			
			$amenazas = $predio->getAmenazas ();
			
			$comuna = new Comunas ( );
			$infoComuna = $comuna->getInfoXY ( $x, $y );
			
			$propietario = $predio->getPropietario ();
			
			$info [] = array ('seccion' => 'General', 'property' => 'Predio', 'value' => $numpredio );
			$info [] = array ('seccion' => 'General', 'property' => 'Propietario', 'value' => implode ( " ", array ($propietario->getApellidos (), $propietario->getNombres () ) ) );
			$info [] = array ('seccion' => 'General', 'property' => 'C.C o NIT', 'value' => $propietario->getNumId () );
			$info [] = array ('seccion' => 'General', 'property' => 'Direccion', 'value' => $predio->getDireccion () );
			$info [] = array ('seccion' => 'General', 'property' => 'Comuna', 'value' => $infoComuna [0] ['value'] );
			$info [] = array ('seccion' => 'General', 'property' => 'Manzana', 'value' => $predio->getManzana () );
			$info [] = array ('seccion' => 'General', 'property' => 'Superficie', 'value' => number_format ( $predio->getAreaM2 (), 1, ',', '.' ) . ' m<small><sup>2</sup></small>' );
			$info [] = array ('seccion' => 'General', 'property' => 'Perimetro', 'value' => number_format ( $predio->getPerimetro (), 1, ',', '.' ) . ' m' );
			
			foreach ( $amenazas as $a ) {
				/* @var $a SII_PotAmenazas */
				$info [] = array ('seccion' => 'Amenazas identificadas', 'property' => 'Clasificacion', 'value' => htmlentities ( $a->getNombreAmenaza () ) );
			}
		
		} else {
			$info [] = array ('seccion' => 'Sin Resultados', 'property' => 'No se encontro informacion', 'value' => '...' );
		}
		
		return $info;
	}
}
?>