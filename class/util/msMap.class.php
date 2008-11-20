<?php
/**
 * 
 * Clase encargada de realizar el manejo sobre el mapa
 * @package util
 *
 */
class msMap {
	
	private $filename;
	private $tmp_file;
	private $mapObj;
	/**
	 * Constructor de la clase
	 *
	 * @param String $map_file_path
	 */
	public function __construct($map_file_path) {
		$this->filename = $map_file_path;
		$this->mapObj = ms_newMapObj ( $map_file_path );
	}
	/**
	 * Asigna el ancho del mapa
	 *
	 * @param Integer $w
	 */
	public function setWidth($w) {
		$this->mapObj->set ( 'width', $w );
	}
	/**
	 * Asigna el alto del mapa
	 * 
	 * @param Integer $h
	 */
	public function setHeight($h) {
		$this->mapObj->set ( 'height', $h );
	}
	/**
	 * Retorna el nombre del mapa
	 *
	 * @return String
	 */
	public function getName() {
		return $this->mapObj->name;
	}
	/**
	 * Retorna un objeto MsMap
	 *
	 * @return MsMap
	 */
	public function getMsObj() {
		return $this->mapObj;
	}
	/**
	 * Retorna la leyenda del mapa
	 *
	 * @return String
	 */
	public function getLegend() {
		return $this->mapObj->legend;
	}
	/**
	 * Retorna el nombre de la capa 
	 *
	 * @param String $layer_name
	 * @return String
	 */
	public function getLayer($layer_name) {
		return $this->mapObj->getLayerByName ( $layer_name );
	}
	/**
	 * Retorna el numero de las capas qeu forman el mapa
	 *
	 * @return Integer
	 */
	public function getNumLayers() {
		return $this->mapObj->numlayers;
	}
	/**
	 * Retorna el ancho de la presentacion del mapa
	 *
	 * @return Integer
	 */
	public function getMapWidth() {
		return $this->mapObj->width;
	}
	/**
	 * Retorna el Alto de la presentacion del mapa
	 *
	 * @return Integer
	 */
	public function getMapHeight() {
		return $this->mapObj->height;
	}
	/**
	 * Retorna la Escala del mapa visualizado
	 *
	 * @return Integer
	 */
	public function getMapScale() {
		return $this->mapObj->scale;
	}
	/**
	 * Retorna la ruta del mapa
	 *
	 * @return String
	 */
	public function getWebImagePath() {
		return $this->mapObj->web->imagepath;
	}
	/**
	 * Retorna el tamano total de la presentacion del mapa
	 *
	 * @param Boolean $as_string
	 * @return String
	 */
	public function getExtent($as_string = false) {
		if ($as_string) {
			$ext = $this->mapObj->extent->minx . " " . $this->mapObj->extent->miny . " " . $this->mapObj->extent->maxx . " " . $this->mapObj->extent->maxy;
			return $ext;
		}
		return $this->mapObj->extent;
	}
	
	/**
	 * Dibuja el mapa.
	 *
	 * @return string | url de la imagen
	 */
	public function drawMap() {
		$image = $this->mapObj->draw ();
		return $image->saveWebImage ();
	}
	/**
	 * Dibuja el mapa de referencia
	 *
	 * @return String url de imagen referencia del mapa
	 */
	public function drawReferenceMap() {
		$image = $this->mapObj->drawReferenceMap ();
		return $image->saveWebImage ();
	}
	
	/**
	 * Dibuja las convenciones del mapa.
	 *
	 * @return string | url de la imagen
	 */
	public function drawLegend() {
		$image = $this->mapObj->drawLegend ();
		return $image->saveWebImage ();
	}
	/**
	 * Intecambia el estatus de la capa
	 *
	 * @param String $layer_name
	 */
	public function toogleLayer($layer_name) {
		
		$layer = $this->getLayer ( $layer_name );
		$status = ($layer->status == MS_ON) ? MS_OFF : MS_ON;
		$layer->set ( 'status', $status );
	}
	/**
	 * Intercambia el estatus 
	 * de los items que componen
	 * la capa
	 *
	 * @param String $layer_name
	 * @param String $class_name
	 */
	public function toogleLayerClass($layer_name, $class_name) {
		
		$layer = $this->getLayer ( $layer_name );
		
		for($i = 0; $i < $layer->numclasses; $i ++) {
			$class = $layer->getClass ( $i );
			
			if ($class->name == $class_name) {
				$status = ($class->status == MS_ON) ? MS_OFF : MS_ON;
				$class->set ( 'status', $status );
			}
		}
	}
	/**
	 * Intercambia el estatus toda 
	 * la capa
	 *
	 * @param String $layer_name
	 */
	public function toggleAllLayerClasses($layer_name) {
		
		$layer = $this->getLayer ( $layer_name );
		
		for($i = 0; $i < $layer->numclasses; $i ++) {
			$class = $layer->getClass ( $i );
			$status = ($class->status == MS_ON) ? MS_OFF : MS_ON;
			$class->set ( 'status', $status );
		}
	}
	/**
	 * Controla la accion de zoom enel mapa
	 *
	 * @param array $params
	 */
	public function processAction($params) {
		
		$action = $params ['action'];
		$extent = explode ( " ", $params ['extent'] );
		$click_x = $params ['x'];
		$click_y = $params ['y'];
		$zoom_factor = 2;
		
		$my_extent = ms_newrectObj ();
		$my_extent->setextent ( $extent [0], $extent [1], $extent [2], $extent [3] );
		
		switch ( $action) {
			case 'zoom-in' :
				$zoom = $zoom_factor * 1;
			break;
			
			case 'zoom-out' :
				$zoom = $zoom_factor * - 1;
			break;
			
			case 'pan' :
				$zoom = 1;
			break;
			
			default :
				$zoom = 0;
		}
		$ptoNewCenter = ms_newpointObj ();
		$ptoNewCenter->setXY ( $click_x, $click_y );
		
		$this->mapObj->zoompoint ( $zoom, $ptoNewCenter, $this->getMapWidth (), $this->getMapHeight (), $my_extent );
	}
	/**
	 * Retorna todas las capas componentes del mapa
	 *
	 * @return array $arrayLayers
	 */
	public function getAllLayers() {
		$arrayLayers = array ( );
		$all_layers = $this->mapObj->getAllLayerNames ();
		
		foreach ( $all_layers as $layer ) {
			$arrayLayers [$layer] = $this->mapObj->getLayerByName ( $layer );
		}
		return $arrayLayers;
	}
	
	/**
	 * Retorna las Capas que esten activas dentro del mapa
	 *
	 * @return array $arrayActives
	 */
	public function getActiveLayers() {
		$all_layers = $this->getAllLayers ();
		$arrayActives = array ( );
		
		foreach ( $all_layers as $layer ) {
			if ($layer->status == MS_ON) {
				$arrayActives [] = $layer;
			}
		}
		return $arrayActives;
	}
	
	/**
	 * Retorna las imagenes de los iconos de la capa correspondiente
	 *
	 * @param String $layer_name
	 * @return array $arrayIcons
	 */
	public function getLayerIcons($layer_name) {
		
		$arrayIcons = array ( );
		$layerObj = $this->mapObj->getLayerByName ( $layer_name );
		$numClasses = $layerObj->numclasses;
		
		for($i = 0; $i < $numClasses; $i ++) {
			$classObj = $layerObj->getClass ( $i );
			$imageObj = $classObj->createLegendIcon ( 16, 18 );
			$url = $imageObj->saveWebImage ();
			$status = ($classObj->status == MS_ON) ? TRUE : FALSE;
			$arrayIcons [] = array ('name' => $classObj->name, 'status' => $status, 'url' => $url );
		}
		
		return $arrayIcons;
	}
	
	/**
	 * Convierte una posicion en pixels a una posicion geografica.
	 *
	 * @param nPixPos  double - La posicion en pixel.
	 * @param dfPixMin double - Valor minimo del mapa en pixels.
	 * @param dfPixMax double - Valor maximo del mapa en pixels.
	 * @param dfGeoMin double - Valor geografico minimo del mapa.
	 * @param dfGeoMax double - Valor geografico maximo del mapa.
	 * @param nInversePix integer - Flag opcional para invertir, setear en 1 para coordenadas en pixels Y donde Izq Sup > Lower Right
	 * @return double - posicion geografica.
	 **/
	public static function pixelToGeo($nPixPos, $dfPixMin, $dfPixMax, $dfGeoMin, $dfGeoMax, $nInversePix = "") {
		// calcula el ancho geografico y en pixels
		$dfWidthGeo = $dfGeoMax - $dfGeoMin;
		$dfWidthPix = $dfPixMax - $dfPixMin;
		
		// calcula la relacion
		$dfPixToGeo = $dfWidthGeo / $dfWidthPix;
		
		if ($nInversePix == "") {
			$dfDeltaPix = $nPixPos - $dfPixMin;
		} else {
			$dfDeltaPix = $dfPixMax - $nPixPos;
		}
		
		$dfDeltaGeo = $dfDeltaPix * $dfPixToGeo;
		$dfPosGeo = $dfGeoMin + $dfDeltaGeo;
		
		return $dfPosGeo;
	}
	/**
	 * Retorna un archivo temporal
	 *
	 * @return msMap
	 */
	public function getTmpFile() {
		return $this->tmp_file;
	}
	/**
	 * Guarda el estado actual del mapa
	 *
	 * @param String $file_path
	 */
	public function saveMapState($file_path) {
		$this->tmp_file = $file_path;
		$this->mapObj->save ( $file_path );
	}
}

?>
