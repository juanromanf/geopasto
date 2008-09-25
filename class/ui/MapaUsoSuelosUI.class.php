<?php

class MapaUsoSuelosUI extends msMapLayout {
	protected $name = "mapa-usosuelos";
	protected $mapname = "usosuelos";
	
	public function createLayout($args) {
		
		$output = parent::createLayout ( $args );
		$layers = array ();
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Zonas', 'clsprefix' => 'Zona ', 'clsdisplay' => 'num_zona', 'clsitem' => 'num_zona' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Comunas', 'clsprefix' => 'Comuna ', 'clsdisplay' => 'num_comuna', 'clsitem' => 'num_comuna' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Areas Homogeneas', 'clsprefix' => '', 'clsdisplay' => 'nombre', 'clsitem' => 'id_area' );
		$layers [] = array ('map' => 'usosuelos', 'layer' => 'Usos Suelos', 'clsprefix' => '', 'clsdisplay' => 'areaactividad', 'clsitem' => 'codareaactividad' );
		
		$this->addSymbols ( $layers );
		
		return $output;
	}
	
	/**
	 * Consulta demarcacion urbanistica.
	 *
	 * @param array $args
	 * @param $args['x']: click X coord.
	 * @param $args['y']: click Y coord.
	 * @param $args['extent']: map extent.
	 */
	public function doQuery($args) {
		/**
		 * TODO: consolidar reporte de demarcacion.
		 */
		try {
			$click_x = $args ['x'];
			$click_y = $args ['y'];
			$extent = explode ( " ", $args ['extent'] );
			$map = $this->getTempMap ();
			
			$x = msMap::pixelToGeo ( $click_x, 0, $map->getMapWidth (), $extent [0], $extent [2] );
			$y = msMap::pixelToGeo ( $click_y, 0, $map->getMapHeight (), $extent [1], $extent [3], true );
			
			$layer = $map->getLayer ( 'Usos Suelos' );
			$db = AppSQL::getInstance ();
			
			$toleracia = 20;
			$x1 = $x - $toleracia;
			$y1 = $y - $toleracia;
			$x2 = $x + $toleracia;
			$y2 = $y + $toleracia;
			
			if ($layer->type == MS_LAYER_POLYGON) {
				// Spatial SQL para layers tipo Polygon
				$query = "SELECT numpredio FROM gis.usos_suelo t
						 WHERE the_geom && 'BOX3D($x1 $y1, $x2 $y2)'::box3d AND
						 Intersects(GeometryFromText( 'POINT($x $y)', -1), t.the_geom)";
			
			} else {
				// Spatial SQL para layers de otro tipo
				$query = "SELECT numpredio FROM gis.usos_suelo t
						 WHERE the_geom && 'BOX3D($x1 $y1, $x2 $y2)'::box3d AND
						 Intersects(Buffer(GeometryFromText( 'POINT($x $y)', -1), 5), t.the_geom)";
			}
			
			$rs = $db->Execute ( $query );
			$numpredio = $rs->fields [0];
			
			$predio = new SII_Predios ( );
			$predio->Load ( "numpredio = '$numpredio'" );
			
			$propietario = $predio->getPropietario ();
			$barrio = $predio->getBarrio ();
			$demarcacion = $predio->getDemarcion ();
			$subsector = $demarcacion->getSubSector ();
			
			$info = array ();
			$info [] = array ('seccion' => '1. General', 'property' => 'Predio', 'value' => $numpredio );
			$info [] = array ('seccion' => '1. General', 'property' => 'Propietario', 'value' => implode ( " ", array ($propietario->getApellidos (), $propietario->getNombres () ) ) );
			$info [] = array ('seccion' => '1. General', 'property' => 'Manzana IGAC', 'value' => $predio->getDemEstrato ()->getManzanaIGAC () );
			$info [] = array ('seccion' => '1. General', 'property' => 'Manzana DANE', 'value' => $predio->getDemEstrato ()->getManzanaDANE () );
			$info [] = array ('seccion' => '1. General', 'property' => 'Direccion', 'value' => $predio->getDireccion () );
			$info [] = array ('seccion' => '1. General', 'property' => 'Barrio', 'value' => $barrio->getNombreBarrio () );
			$info [] = array ('seccion' => '1. General', 'property' => 'Comuna', 'value' => $barrio->getComuna () );
			
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Clase de suelo', 'value' => $demarcacion->getClaseSuelo ()->getNombreSuelo () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Area actividad', 'value' => $demarcacion->getAreaActividad ()->getNombreArea () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Tratamiento', 'value' => $demarcacion->getTratamiento ()->getNombreTratamiento () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Sector', 'value' => $subsector->getSector ()->getNombreSector () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'SubSector', 'value' => $subsector->getNombreSubsector () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Pieza urbana', 'value' => $subsector->getSector ()->getPiezaUrbana ()->getNombrePieza () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Io', 'value' => $demarcacion->getIocupacion () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Ic', 'value' => $demarcacion->getIconstruccion () );
			$info [] = array ('seccion' => '2. Normatividad', 'property' => 'Ics', 'value' => $demarcacion->getIcesion () );
			
			$info [] = array ('seccion' => '3. Lineas Paramentales', 'property' => 'Tipologia via', 'value' => $demarcacion->getTipologia()->getNombreTipologia());
			$info [] = array ('seccion' => '3. Lineas Paramentales', 'property' => 'Distancia del eje', 'value' => $demarcacion->getDistanciaEje());
		
		} catch ( Exception $e ) {
			throw new Exception ( 'MapaUsoSuelosUI.doQuery() - ' . $e->getMessage () );
		}
		return json_encode ( $info );
	}
}
?>