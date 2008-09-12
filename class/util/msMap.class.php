<?php

class msMap {
	
	private $filename;
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
	
	public function getExtent($as_string = false) {
		if ($as_string) {
			$ext = $this->mapObj->extent->minx . " " . $this->mapObj->extent->miny . " " . $this->mapObj->extent->maxx . " " . $this->mapObj->extent->maxy;
			return $ext;
		}
		return $this->mapObj->extent;
	}
	
	public function drawMap() {
		$image = $this->mapObj->draw ();
		return $image->saveWebImage ();
	}
	
	public function toogleLayer($layer_name, $status) {
		$layer = $this->getLayer ( $layer_name );
		if ($status == 'true') {
			$layer->set ( 'status', MS_ON );
		} else {
			$layer->set ( 'status', MS_OFF );
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
	
	public function getLayerIcons($layer_name) {
		
		$arrayIcons = array ();
		$layerObj = $this->mapObj->getLayerByName ( $layer_name );
		$numClasses = $layerObj->numclasses;
		
		for($i = 0; $i < $numClasses; $i ++) {
			$classObj = $layerObj->getClass ( $i );
			$imageObj = $classObj->createLegendIcon ( 16, 18 );
			$url = $imageObj->saveWebImage ();
			
			$arrayIcons [] = array ('name' => $classObj->name, "url" => $url );
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
	
	public function saveMapState($file_path) {
		$this->mapObj->save ( $file_path );
	}
}

?>
