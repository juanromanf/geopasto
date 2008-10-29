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
			$predio1 = new SII_Predios ( );
			$predio = new UsosSuelos();
			
			$predio1->Load ( "numpredio = '$numpredio'" );
			$predio->Load ( "numpredio = '$numpredio'" );
			$this->Load ( "numpredio = '$numpredio'" );
			
			$amenazas = $predio1->getAmenazas ();
			
			$comuna = new Comunas ( );
			$infoComuna = $comuna->getInfoXY ( $x, $y );
			
			$propietario = $predio1->getPropietario ();
			
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Predio', 'value' => $numpredio );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Propietario', 'value' => implode ( " ", array ($propietario->getApellidos (), $propietario->getNombres () ) ) );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'C.C o NIT', 'value' => $propietario->getNumId () );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Direccion', 'value' => $predio1->getDireccion () );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Comuna', 'value' => $infoComuna [0] ['value'] );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Manzana', 'value' => $predio1->getManzana () );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Superficie', 'value' => number_format ( $predio->getAreaM2 (), 1, ',', '.' ) . ' m<small><sup>2</sup></small>' );
			$info [] = array ('seccion' => 'Datos del Predio', 'property' => 'Perimetro', 'value' => number_format ( $predio->getPerimetro (), 1, ',', '.' ) . ' m' );
			
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