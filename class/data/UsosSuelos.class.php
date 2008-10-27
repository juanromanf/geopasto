<?php

class UsosSuelos extends AppActiveRecord {
	public $_table = 'gis.usos_suelo';
	
	public function __construct($xajaxResponse = false) {
		$keys = array ('gid', 'numpredio' );
		parent::__construct ( $xajaxResponse, FALSE, $keys );
	}
	
	public function getCodAreaActividad() {
		return $this->codareaactividad;
	}
	
	public function getAreaActividad() {
		return $this->areaactividad;
	}
	
	public function getSiglaArea() {
		return $this->sigla;
	}
	
	public function getAreaM2() {
		try {
			$sql = "SELECT area(the_geom) FROM gis.predios p 
					WHERE p.num_predial = '$this->numpredio'";
			$db = AppSQL::getInstance ();
			$rs = $db->Execute ( $sql );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $rs->fields [0];
	}
	
	public function getPerimetro() {
		try {
			$sql = "SELECT perimeter(the_geom) FROM gis.predios p 
					WHERE p.num_predial = '$this->numpredio'";
			$db = AppSQL::getInstance ();
			$rs = $db->Execute ( $sql );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $rs->fields [0];
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotUsosAreaActividad
	 */
	public function getUsosPrincipales() {
		$obj = new SII_PotUsosAreaActividad ( );
		$rs = $obj->Find ( 'codareaactividad = ' . $this->getCodAreaActividad () . " and tipousu = 'P'" );
		
		return $rs;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotUsosAreaActividad
	 */
	public function getUsosCondicionados() {
		$obj = new SII_PotUsosAreaActividad ( );
		$rs = $obj->Find ( 'codareaactividad = ' . $this->getCodAreaActividad () . " and tipousu = 'C'" );
		
		return $rs;
	}
	
	public function getTotalAreaByActividad($codactividad) {
		
		try {
			$sql = "SELECT sum(area(the_geom)) FROM gis.usos_suelo u 
					WHERE u.codareaactividad = '$codactividad'";
			$db = AppSQL::getInstance ();
			$rs = $db->Execute ( $sql );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		return $rs->fields [0];
	}
	
	public function getInfoXY($x, $y) {
		$toleracia = 20;
		$x1 = $x - $toleracia;
		$y1 = $y - $toleracia;
		$x2 = $x + $toleracia;
		$y2 = $y + $toleracia;
		
		$info = array ();
		// Spatial SQL para layers tipo Polygon
		$query = "SELECT numpredio FROM gis.usos_suelo t
						 WHERE the_geom && 'BOX3D($x1 $y1, $x2 $y2)'::box3d AND
						 Intersects(GeometryFromText( 'POINT($x $y)', -1), t.the_geom)";
		
		$db = AppSQL::getInstance ();
		$rs = $db->Execute ( $query );
		$numpredio = $rs->fields [0];
		
		if ($numpredio) {
			$predio = new SII_Predios ( );
			$predio->Load ( "numpredio = '$numpredio'" );
			$this->Load ( "numpredio = '$numpredio'" );
			
			$comuna = new Comunas ( );
			$infoComuna = $comuna->getInfoXY ( $x, $y );
			
			$areaH = new AreasHomogeneas ( );
			$infoAreaH = $areaH->getInfoXY ( $x, $y );
			
			$propietario = $predio->getPropietario ();
			
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Predio', 'value' => $numpredio );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Propietario', 'value' => htmlentities ( implode ( " ", array ($propietario->getApellidos (), $propietario->getNombres () ) ) ) );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'C.C o NIT', 'value' => $propietario->getNumId () );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Direccion', 'value' => htmlentities ( $predio->getDireccion () ) );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Comuna', 'value' => $infoComuna [0] ['value'] );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Manzana', 'value' => $predio->getManzana () );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Superficie', 'value' => number_format ( $this->getAreaM2 (), 1, ',', '.' ) . ' m<small><sup>2</sup></small>' );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Perimetro', 'value' => number_format ( $this->getPerimetro (), 1, ',', '.' ) . ' m' );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Area morfologica homogenea', 'value' => $infoAreaH [0] ['value'] );
			$info [] = array ('seccion' => '1. Reglamentacion P.O.T', 'property' => 'Area de actividad', 'value' => '(' . $this->getSiglaArea () . ') ' . htmlentities ( $this->getAreaActividad () ) );
			
			$usosP = $this->getUsosPrincipales ();
			foreach ( $usosP as $uso ) {
				/* @var $uso SII_PotUsosAreaActividad */
				$info [] = array ('seccion' => '2. Usos principales', 'property' => 'Sigla', 'value' => $uso->getSigla (), 'extra' => htmlentities ( $uso->getImpacto ()->getDescripcion () ) );
			}
			
			$usosC = $this->getUsosCondicionados ();
			foreach ( $usosC as $uso ) {
				/* @var $uso SII_PotUsosAreaActividad */
				$info [] = array ('seccion' => '3. Usos condicionados', 'property' => 'Sigla', 'value' => $uso->getSigla (), 'extra' => htmlentities ( $uso->getImpacto ()->getDescripcion () ) );
			}
		
		} else {
			$info [] = array ('seccion' => 'Sin Resultados', 'property' => 'No se encontro informacion', 'value' => '...' );
		}
		
		return $info;
	}
}
?>