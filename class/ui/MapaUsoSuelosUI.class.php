<?php
/**
 * 
 * Es una clase que implementa el patron de 
 * msMapLayout para mostrar la pagina de MapaUsoSuelos
 * 
 * @package ui
 * 
 */
class MapaUsoSuelosUI extends msMapLayout {
	protected $name = "mapa-usosuelos";
	protected $mapname = "usosuelos";
	/**
	 * Crea la interfaz de usuario 
	 * para el trabajo del mapa
	 *
	 * @param array $args
	 * @return String que contiene el HTML
	 */
	public function createLayout($args) {
		
		$output = parent::createLayout ( $args );
		$this->processSymbols ();
		return $output;
	}
	/**
	 * 
	 * Carga los simbolos pertenecientes al mapa
	 *
	 */
	public function processSymbols() {
		$layers = array ();
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Zonas', 'clsprefix' => 'Zona ', 'clsdisplay' => 'num_zona', 'clsitem' => 'num_zona' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Comunas', 'clsprefix' => 'Comuna ', 'clsdisplay' => 'num_comuna', 'clsitem' => 'num_comuna' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Areas Homogeneas', 'clsprefix' => '', 'clsdisplay' => 'nombre', 'clsitem' => 'id_area' );
		$layers [] = array ('map' => 'usosuelos', 'layer' => 'Usos Suelos', 'clsprefix' => '', 'clsdisplay' => 'areaactividad', 'clsitem' => 'codareaactividad' );
		
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
					$info = $obj->getInfoXY ( $x, $y );
					break;
				
				case 'q-comuna' :
					$obj = new Comunas ( );
					$info = $obj->getInfoXY ( $x, $y );
					break;
				
				case 'q-area-homo' :
					$obj = new AreasHomogeneas ( );
					$info = $obj->getInfoXY ( $x, $y );
					break;
				
				case 'q-actividad' :
					$obj = new UsosSuelos ( );
					$info = $obj->getInfoXY ( $x, $y );
					break;
				
				case 'area-residencial' :
					$uso = new UsosSuelos ( );
					$area = $uso->getTotalAreaByActividad ( '8' );
					$info [] = array ('seccion' => 'Area Residencial Total', 'property' => 'Superficie', 'value' => number_format ( $area, 1, ',', '.' ) . ' m<small><sup>2</sup></small>' );
					break;
				
				case 'area-comercial' :
					$uso = new UsosSuelos ( );
					$area = $uso->getTotalAreaByActividad ( '3' );
					$info [] = array ('seccion' => 'Area Comercial Total', 'property' => 'Superficie', 'value' => number_format ( $area, 1, ',', '.' ) . ' m<small><sup>2</sup></small>' );
					break;
				
				case 'area-zonas-verdes' :
					$uso = new UsosSuelos ( );
					$area = $uso->getTotalAreaByActividad ( '25' );
					$info [] = array ('seccion' => 'Area Zonas Verdes Total', 'property' => 'Superficie', 'value' => number_format ( $area, 1, ',', '.' ) . ' m<small><sup>2</sup></small>' );
					break;
			}
			
			$arrayInfo = array_merge ( $arrayInfo, $info );
		
		} catch ( Exception $e ) {
			throw new Exception ( 'MapaUsoSuelosUI.doQuery() - ' . $e->getMessage () );
		}
		return json_encode ( $arrayInfo );
	}
}
?>