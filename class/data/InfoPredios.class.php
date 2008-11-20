<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla informacion_predios
 * 
 * @package data
 *
 */
class InfoPredios extends AppActiveRecord {
	public $_table = 'app.informacion_predios';
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
		
		$info = array ();
		// Spatial SQL para layers tipo Polygon
		$query = "SELECT num_predial FROM gis.predios t
						 WHERE the_geom && 'BOX3D($x1 $y1, $x2 $y2)'::box3d AND
						 Intersects(GeometryFromText( 'POINT($x $y)', -1), t.the_geom)";
		
		$db = AppSQL::getInstance ();
		$rs = $db->Execute ( $query );
		$numpredio = $rs->fields [0];
		
		$info = array ();
		if ($numpredio) {
			$this->Load ( "numpredio = '$numpredio'" );
			
			$obj = new SII_PotAreasActividad ( );
			$obj->Load ( "codareaactividad = '" . $this->codareaactividad . "'" );
			
			$item = $this->toArray ();
			//$item ['areaactividad'] = '(' . $obj->getSigla () . ') ' . strtoupper ( htmlentities ( $obj->getNombreArea () ) );
			$item ['areaactividad'] = strtoupper ( htmlentities ( $obj->getNombreArea () ) );
			$info [] = $item;
		}
		
		return json_encode ( $info );
	}

	/**
	 * Permite hacer una modificacion del 
	 * area de actividad de un predio
	 *
	 * @param int $numpredio
	 * @param int $codareaactividad
	 */
	
	public function modify($numpredio, $codareaactividad) {
		try {
			$this->Load ( "numpredio = '$numpredio'" );
			$this->codareaactividad = $codareaactividad;
			$this->Update ();
			
			$js = "	Ext.getCmp('edit-win').close();
					Ext.getCmp('usosuelos-panel').setActiveAction('pan');
					Ext.getCmp('usosuelos-panel').onMouseClick();
					Ext.getCmp('usosuelos-panel').setActiveAction('query');";
			$this->getXajaxResponse ()->script ( $js );
		
		} catch ( Exception $e ) {
			throw new Exception ( __CLASS__ . '->' . __METHOD__ . " - " . $e->getMessage () );
		}
	}

}
?>