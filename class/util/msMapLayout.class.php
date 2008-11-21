<?php
/**
 * Clase abstracta responsable de la contruccion las
 * herramientas y procesar las acciones que el usuario realiza
 * sobre los distintos mapas que se encuentran el sistema.
 * 
 * @package util
 */
abstract class msMapLayout extends AppPage {
	
	/**
	 * Crea la estructura de la interfaz comun para todos los mapas del sistema.
	 *
	 * @param array $args $arg['mapname'] nombre del archivo de configuracion del mapa.
	 * @return string HTML con los elementos que componen la interfaz.
	 */
	public function createLayout($args) {
		$mapfile = $args ['map'];
		
		$mapObj = new msMap ( $mapfile );
		$mapObj->drawReferenceMap ();
		
		if (! isset ( $_SESSION [$this->mapname . '_temp'] )) {
			$_SESSION [$this->mapname . '_temp'] = $this->mapname . '_temp_' . time () . '.map';
		}
		$mapObj->saveMapState ( '../tmp/' . $_SESSION [$this->mapname . '_temp'] );
		
		$this->tpl->assign ( 'map', $mapObj );
		
		$template = 'app.maplayout.html';
		$output = $this->tpl->fetch ( $template );
		
		$js = "Ext.getCmp('" . $mapObj->getName () . "-panel').maskPanel(false); ";
		$this->getXajaxResponse ()->assign ( $mapObj->getName () . '-reference', 'src', $mapObj->drawReferenceMap());
		$this->getXajaxResponse ()->assign ( $mapObj->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round ( $mapObj->getMapScale (), 0 ) );
		$this->getXajaxResponse ()->script ( $js );
		
		return $output;
	}
	
	/**
	 * Retorna una intancia de la Clase msMap, desde el archivo
	 * temporal donde se almacena el estado actual.
	 *
	 * @return msMap
	 */
	public function getTempMap() {
		$map = new msMap ( 'tmp/' . $_SESSION [$this->mapname . '_temp'] );
		return $map;
	}
	
	/**
	 * Almacena el estado del mapa.
	 *
	 * @param msMap $msMapObj
	 */
	public function saveTempMap(msMap $msMapObj) {
		$msMapObj->saveMapState ( $_SESSION [$this->mapname . '_temp'] );
	}
	
	/**
	 * Procesa las convenciones correspondientes a cada capa del mapa.
	 * Debe ser sobre cargado en las clases derivadas.
	 *
	 */
	public function processSymbols() {
		// override this method
	}
	
	/**
	 * Funcion para restaurar el mapa a su estado inicial.
	 *
	 * @param string $map_file ruta al archivo de definicion del mapa.
	 */
	public function restoreMap($map_file) {
		$map = new msMap ( $map_file );
		$map->saveMapState ( '../tmp/' . $_SESSION [$this->mapname . '_temp'] );
		$this->processSymbols ();
		
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'src', $map->drawMap () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-reference', 'src', $map->drawReferenceMap());
		$this->getXajaxResponse ()->assign ( $map->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round ( $map->getMapScale (), 0 ) );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-ex', 'value', $map->getExtent ( TRUE ) );
		
		$js = "Ext.getCmp('" . $map->getName () . "-panel').maskPanel(false);";
		$js .= "Ext.getCmp('" . $map->getName () . "-panel').reloadLayersTree();";
		$this->getXajaxResponse ()->script ( $js );
	}
	
	/**
	 * Cambia el tamano del mapa.
	 *
	 * @param string $size 640x480 - 800x600 ...
	 */
	public function resizeMap($size) {
		$map = $this->getTempMap ();
		list ( $w, $h ) = explode ( "x", $size );
		
		$map->setHeight ( $h );
		$map->setWidth ( $w );
		
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'width', $map->getMapWidth () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'height', $map->getMapHeight () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'src', $map->drawMap () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-reference', 'src', $map->drawReferenceMap());
		$this->getXajaxResponse ()->assign ( $map->getName () . '-div', 'style.width', ($w + 2) . "px" );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-div', 'style.height', ($h + 2) . "px" );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-ex', 'value', $map->getExtent ( TRUE ) );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round ( $map->getMapScale (), 0 ) );
		
		$this->saveTempMap ( $map );
		
		$js = "Ext.getCmp('" . $map->getName () . "-panel').maskPanel(false);";
		$this->getXajaxResponse ()->script ( $js );
	}
	
	/**
	 * Delega las acciones ejecutadas sobre el mapa por parte del usuario.  
	 *
	 * @param array $args
	 */
	public function doAction($args) {
		$map = $this->getTempMap ();
		$map->drawReferenceMap ();
		$action = isset ( $args ['action'] ) ? $args ['action'] : 'process';
		
		switch ($action) {
			case 'toggle-layer' :
				$layer_name = $args ['layer'];
				$map->toogleLayer ( $layer_name );
				break;
			
			case 'toggle-class' :
				$layer_name = $args ['layer'];
				$class_name = $args ['classi'];
				$map->toogleLayerClass ( $layer_name, $class_name );
				break;
			
			case 'toggle-all-classes' :
				$layer_name = $args ['layer'];
				$map->toggleAllLayerClasses ( $layer_name );
				break;
			
			default :
				$map->processAction ( $args );
				break;
		}
		
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'src', $map->drawMap () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-reference', 'src', $map->drawReferenceMap());
		$this->getXajaxResponse ()->assign ( $map->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round ( $map->getMapScale (), 0 ) );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-ex', 'value', $map->getExtent ( TRUE ) );
		
		$this->saveTempMap ( $map );
		
		$js = "Ext.getCmp('" . $map->getName () . "-panel').maskPanel(false);";
		$this->getXajaxResponse ()->script ( $js );
	}
	
	/**
	 * Contruye un string en formato JSON que contiene la estructura de arbol
	 * que representa las capas que componen el mapa.
	 *
	 * @return string
	 */
	public function getLayers() {
		$map = $this->getTempMap ();
		
		$arrayLayers = $map->getAllLayers ();
		$tree = array ();
		
		foreach ( $arrayLayers as $layer ) {
			if ($layer->name != 'Norte') {
				$node = array ();
				$node ['id'] = $map->getName () . '-l-' . $layer->name;
				$node ['text'] = $layer->name;
				$node ['iconCls'] = 'icon-16-view-presentation';
				$node ['checked'] = $layer->status ? TRUE : FALSE;
				$node ['expanded'] = FALSE;
				$node ['leaf'] = FALSE;
				$node ['children'] = array ();
				
				$arrayIcons = $map->getLayerIcons ( $layer->name );
				
				foreach ( $arrayIcons as $icon ) {
					$item = array ();
					$item ['text'] = $icon ['name'];
					$item ['icon'] = $icon ['url'];
					$item ['leaf'] = TRUE;
					$item ['checked'] = $icon ['status'];
					
					$node ['children'] [] = $item;
				}
				
				$tree [] = $node;
			}
		}
		return json_encode ( $tree );
	}
	
	/**
	 * Se encarga de realizar una busqueda rapida sobre el mapa
	 * con los requerimientos del usuario.
	 * Retorna un string en formato JSON con los resultados para su
	 * presentacion.
	 * 
	 * $args['layer']: capa donde se realiza la busqueda.
	 * $args['text']: texto a buscar.
	 * 
	 * @param array $args
	 * @return string JSON con los resultados.
	 */
	public function quickSearch($args) {
		$layer_name = $args ['layer'];
		$text = $args ['text'];
		
		$map = $this->getTempMap ();
		$mapLayer = $map->getLayer ( $layer_name );
		list ( $the_geom, $table ) = explode ( " from ", $mapLayer->data );
		
		$class = str_replace ( " ", "", $layer_name );
		$record = new $class ( );
		$fields = $record->GetAttributeNames ();
		
		$where = "";
		foreach ( $fields as $field ) {
			if ($field != $the_geom) {
				$where .= "$field like '$text' or ";
			}
		}
		$where = substr ( $where, 0, strlen ( $where ) - 3 );
		
		$db = AppSQL::getInstance ();
		$results = $record->Find ( $where );
		
		// new layer for results
		$layer = ms_newLayerObj ( $map->getMsObj () );
		$layer->set ( 'name', "Busqueda en $layer_name: $text" );
		$layer->set ( 'type', $mapLayer->type );
		$layer->set ( 'connectiontype', $mapLayer->connectiontype );
		$layer->set ( 'connection', $mapLayer->connection );
		$layer->set ( 'data', $mapLayer->data );
		$layer->set ( 'status', MS_ON );
		
		$rows = array ();
		$i = 0;
		foreach ( $results as $rec ) {
			$node = array ();
			foreach ( $fields as $field ) {
				if ($field != $the_geom) {
					$node [$field] = $rec->$field;
				}
			}
			$ext = $db->GetRow ( "select extent($the_geom) from $table where gid = " . $rec->gid );
			$str = str_replace ( "BOX(", "", $ext [0] );
			$str = str_replace ( ")", "", $str );
			$str = str_replace ( ",", " ", $str );
			$node ['extent'] = $str;
			
			$rows [] = $node;
			
			// Resaltar resultados
			//-- class
			$gid = $rec->gid;
			$class = ms_newClassObj ( $layer );
			$class->set ( "name", "RESULTADO " . ++ $i );
			$class->setExpression ( "([gid] = $gid)" );
			
			//-- style
			$style = ms_newStyleObj ( $class );
			$style->set ( 'symbolname', 'border2' );
			$style->set ( 'size', 2 );
			$style->color->setRGB ( 255, 0, 0 );
		}
		
		$this->saveTempMap ( $map );
		
		$_reader_fields = array ();
		foreach ( $fields as $field ) {
			switch ($field) {
				case 'gid' :
				case 'oid' :
					$_reader_fields [] = array ('name' => $field, 'dataIndex' => $field, 'header' => $field, 'hidden' => TRUE );
					break;
				
				case $the_geom :
					break;
				
				default :
					$_reader_fields [] = array ('name' => $field, 'dataIndex' => $field, 'header' => $field );
					break;
			}
		}
		$_reader_fields [] = array ('name' => 'extent', 'dataIndex' => 'extent', 'header' => 'extent', 'hidden' => TRUE );
		
		$json ['metaData'] = Array ('root' => 'rows', 'totalProperty' => "total", 'fields' => $_reader_fields );
		$json ['total'] = count ( $results );
		$json ['rows'] = $rows;
		
		return json_encode ( $json );
	}

	/**
	 * 
	 * Adiciona las convenciones pertenecientes a cada una de las capas del mapa.
	 * 
	 * $layers[]['map']: nombre del mapa.
	 * $layers[]['layer']: nombre de la capa.
	 * $layers[]['clsitem']: nombre del atributo que define los nombres de los items de cada capa.
	 * $layers[]['clsprefix']: prefijo para los nombres de los items.
	 * $layers[]['clsdisplay']: nombre del atributo que sera mostrado como informacion en el mapa.
	 * 
	 * @param array $layers
	 */
	protected function addSymbols($layers) {
		
		$map = $this->getTempMap ();
		
		foreach ( $layers as $item ) {
			$map_name = $item ['map'];
			$layer_name = $item ['layer'];
			$cls_item = $item ['clsitem'];
			$cls_prefix = $item ['clsprefix'];
			$cls_display = $item ['clsdisplay'];
			
			$layer = $map->getLayer ( $layer_name );
			
			//-- remove all previous classes
			for($i = 0; $i <= $layer->numclasses; $i ++) {
				$layer->removeClass ( 0 );
			}
			
			$convenciones = new Convenciones ( );
			$arraySym = $convenciones->getAll ( $map_name, $layer_name );
			
			$objName = str_replace ( " ", "", $layer_name );
			$layerObj = new $objName ( );
			
			foreach ( $arraySym as $rec ) {
				/* @var $rec Convenciones */
				$key = $rec->keyvalue;
				$op = $rec->operator;
				$layerObj->Load ( "$cls_item = $key" );
				$display = (strlen ( $rec->display ) > 0) ? $rec->display : $cls_prefix . $layerObj->$cls_display;
				
				$symbol = $rec->getSymbol ();
				list ( $cr, $cg, $cb ) = explode ( " ", $symbol->getColor () );
				list ( $or, $og, $ob ) = explode ( " ", $symbol->getOutLineColor () );
				
				//-- class
				$class = ms_newClassObj ( $layer );
				$class->set ( "name", $this->cleanText ( $display ) );
				$class->setExpression ( "([$cls_item] $op $key)" );
				
				//-- label
				$label = $class->label;
				$label->set ( "type", MS_TRUETYPE );
				$label->set ( "font", "trebuc" );
				$label->set ( "size", 8 );
				$label->color->setRGB ( 0, 0, 0 );
				$label->shadowcolor->setRGB ( 230, 230, 230 );
				$label->outlinecolor->setRGB ( 230, 230, 230 );
				$label->outlinecolor->setRGB ( 230, 230, 230 );
				$label->set ( "shadowsizex", 0.5 );
				$label->set ( "shadowsizey", 0.5 );
				$label->set ( "backgroundshadowsizex", 1.5 );
				$label->set ( "backgroundshadowsizey", 1.5 );
				
				//-- style
				$style = ms_newStyleObj ( $class );
				$style->set ( "symbolname", $symbol->getName () );
				$style->set ( "size", $symbol->getSize () );
				$style->set ( "width", $symbol->getWidth () );
				$style->color->setRGB ( $cr, $cg, $cb );
				$style->outlinecolor->setRGB ( $or, $og, $ob );
			}
		}
		$this->saveTempMap ( $map );
	}
	
	/**
	 * Permite hacer una limpieza de caracteres con acentos.
	 *
	 * @param string $text
	 * @return string
	 */
	private function cleanText($text) {
		$text = strtolower ( $text );
		$text = str_replace ( 'á', 'a', $text );
		$text = str_replace ( 'é', 'e', $text );
		$text = str_replace ( 'í', 'i', $text );
		$text = str_replace ( 'ó', 'o', $text );
		$text = str_replace ( 'ú', 'u', $text );
		$text = str_replace ( 'ñ', 'n', $text );
		$text = str_replace ( 'Ñ', 'N', $text );
		
		return strtoupper ( $text );
	}
}
?>