<?php

class msMap {
	
	private $filename;
	private $tmp_file;
	private $mapObj;
	
	public function __construct($map_file_path) {
		$this->filename = $map_file_path;
		$this->mapObj = ms_newMapObj ( $map_file_path );
	}
	
	public function setWidth($w) {
		$this->mapObj->set ( 'width', $w );
	}
	
	public function setHeight($h) {
		$this->mapObj->set ( 'height', $h );
	}
	
	public function getName() {
		return $this->mapObj->name;
	}
	
	public function getMsObj() {
		return $this->mapObj;
	}
	
	public function getLegend() {
		return $this->mapObj->legend;
	}
	
	public function getLayer($layer_name) {
		return $this->mapObj->getLayerByName ( $layer_name );
	}
	
	public function getNumLayers() {
		return $this->mapObj->numlayers;
	}
	
	public function getMapWidth() {
		return $this->mapObj->width;
	}
	
	public function getMapHeight() {
		return $this->mapObj->height;
	}
	
	public function getMapScale() {
		return $this->mapObj->scale;
	}
	
	public function getWebImagePath() {
		return $this->mapObj->web->imagepath;
	}
	
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
	
	public function toogleLayer($layer_name) {
		
		$layer = $this->getLayer ( $layer_name );
		$status = ($layer->status == MS_ON) ? MS_OFF : MS_ON;
		$layer->set ( 'status', $status );
	}
	
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
	
	public function toggleAllLayerClasses($layer_name) {
		
		$layer = $this->getLayer ( $layer_name );
		
		for($i = 0; $i < $layer->numclasses; $i ++) {
			$class = $layer->getClass ( $i );
			$status = ($class->status == MS_ON) ? MS_OFF : MS_ON;
			$class->set ( 'status', $status );
		}
	}
	
	public function processAction($params) {
		
		$action = $params ['action'];
		$extent = explode ( " ", $params ['extent'] );
		$click_x = $params ['x'];
		$click_y = $params ['y'];
		$zoom_factor = 2;
		
		$my_extent = ms_newrectObj ();
		$my_extent->setextent ( $extent [0], $extent [1], $extent [2], $extent [3] );
		
		switch ($action) {
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
	
	public function getAllLayers() {
		$arrayLayers = array ();
		$all_layers = $this->mapObj->getAllLayerNames ();
		
		foreach ( $all_layers as $layer ) {
			$arrayLayers [$layer] = $this->mapObj->getLayerByName ( $layer );
		}
		return $arrayLayers;
	}
	
	public function getActiveLayers() {
		$all_layers = $this->getAllLayers ();
		$arrayActives = array ();
		
		foreach ( $all_layers as $layer ) {
			if ($layer->status == MS_ON) {
				$arrayActives [] = $layer;
			}
		}
		return $arrayActives;
	}
	
	public function getLayerIcons($layer_name) {
		
		$arrayIcons = array ();
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
	
	public function getTmpFile() {
		return $this->tmp_file;
	}
	
	public function saveMapState($file_path) {
		$this->tmp_file = $file_path;
		$this->mapObj->save ( $file_path );
	}
}

?>
