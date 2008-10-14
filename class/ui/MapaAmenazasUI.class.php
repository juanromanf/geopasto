<?php

class MapaAmenazasUI extends msMapLayout {
	protected $name = "mapa-amenazas";
	protected $mapname = "amenazas";
	
	public function createLayout($args) {
		
		$output = parent::createLayout ( $args );
		$this->processSymbols ();
		return $output;
	}
	
	public function processSymbols() {
		$layers = array ();
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Comunas', 'clsprefix' => 'Comuna ', 'clsdisplay' => 'num_comuna', 'clsitem' => 'num_comuna' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Areas Homogeneas', 'clsprefix' => '', 'clsdisplay' => 'nombre', 'clsitem' => 'id_area' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Amenazas', 'clsprefix' => '', 'clsdisplay' => 'descripcion', 'clsitem' => 'codamenaza' );
		
		$this->addSymbols ( $layers );
	}
	
	/**
	 * Consulta demarcacion urbanistica.
	 *
	 * @param array $args
	 * @param $args['x']: click X coord.
	 * @param $args['y']: click Y coord.
	 * @param $args['query']: query to execute.
	 * @param $args['extent']: map extent.
	 */
	public function doQuery($args) {
		
		try {
			$click_x = $args ['x'];
			$click_y = $args ['y'];
			$query = $args ['query'];
			$extent = explode ( " ", $args ['extent'] );
			$map = $this->getTempMap ();
			
			$x = msMap::pixelToGeo ( $click_x, 0, $map->getMapWidth (), $extent [0], $extent [2] );
			$y = msMap::pixelToGeo ( $click_y, 0, $map->getMapHeight (), $extent [1], $extent [3], true );
			
			$arrayInfo = array ();
			
			switch ($query) {
				case 'q-zona' :
					$obj = new Zonas ( );
					break;
				
				case 'q-comuna' :
					$obj = new Comunas ( );
					break;
				
				case 'q-area-homo' :
					$obj = new AreasHomogeneas ( );
					break;
				
				case 'q-amenazas' :
					$obj = new Amenazas ( );
					break;
			}
			$info = $obj->getInfoXY ( $x, $y );
			$arrayInfo = array_merge ( $arrayInfo, $info );
		
		} catch ( Exception $e ) {
			throw new Exception ( 'MapaAmenazasUI.doQuery() - ' . $e->getMessage () );
		}
		return json_encode ( $arrayInfo );
	}
}
?>