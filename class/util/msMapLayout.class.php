<?php

abstract class msMapLayout extends AppPage {
	protected $name = '';
	
	public function createLayout($args) {
		$mapfile = $args ['map'];
		
		$mapObj = new msMap ( $mapfile );
		
		if (! isset ( $_SESSION ['temp_file'] )) {
			$_SESSION ['temp_file'] = time () . '.map';
		}
		$mapObj->saveMapState ( '../tmp/' . $_SESSION ['temp_file'] );
		
		$this->tpl->assign ( 'map', $mapObj );
		
		$template = 'app.maplayout.html';
		$output = $this->tpl->fetch ( $template );
		
		$js = "Ext.getCmp('" . $mapObj->getName () . "-panel').maskPanel(false); ";
		$this->getXajaxResponse ()->assign ( $mapObj->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round($mapObj->getMapScale(), 0));
		$this->getXajaxResponse ()->script ( $js );
		
		return $output;
	}
	
	public function resizeMap($size) {
		$map = new msMap ( 'tmp/' . $_SESSION ['temp_file'] );
		list ( $w, $h ) = explode ( "x", $size );
		
		$map->setHeight ( $h );
		$map->setWidth ( $w );
		
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'width', $map->getMapWidth () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'height', $map->getMapHeight () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'src', $map->drawMap () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-ex', 'value', $map->getExtent ( TRUE ) );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round($map->getMapScale(), 0));
		
		$map->saveMapState ( $_SESSION ['temp_file'] );
		
		$js = "Ext.getCmp('" . $map->getName () . "-panel').maskPanel(false);";
		$this->getXajaxResponse ()->script ( $js );
	}
	
	public function doAction($args) {
		$map = new msMap ( 'tmp/' . $_SESSION ['temp_file'] );
		
		if (isset($args['layer'] )) {
			$layer_name = $args ['layer'];
			$status = $args ['status'];
			
			$layer = $map->getLayer ( $layer_name );
			
			if ($status == 'true') {
				$layer->set ( 'status', MS_ON );
			} else {
				$layer->set ( 'status', MS_OFF );
			}
		
		} else {
			$map->processAction ( $args );
		}
		
		$this->getXajaxResponse ()->assign ( $map->getName () . '-img', 'src', $map->drawMap () );
		$this->getXajaxResponse ()->assign ( $map->getName () . '-scale', 'innerHTML', 'Escala: 1:' . round($map->getMapScale(), 0));
		$this->getXajaxResponse ()->assign ( $map->getName () . '-ex', 'value', $map->getExtent ( TRUE ) );
		
		$map->saveMapState ( $_SESSION ['temp_file'] );
		
		$js = "Ext.getCmp('" . $map->getName () . "-panel').maskPanel(false);";
		$this->getXajaxResponse ()->script ( $js );
	}
	
	public function getLayers() {
		$map = new msMap ( 'tmp/' . $_SESSION ['temp_file'] );
		
		$arrayLayers = $map->getAllLayers ();
		$tree = array ();
		
		foreach ( $arrayLayers as $layer ) {
			$node = array ();
			$node ['id'] = $map->getName () . '-l-' . $layer->name;
			$node ['text'] = $layer->name;
			$node ['icon'] = $map->getLayerIcon ( $layer->name );
			$node ['leaf'] = TRUE;
			$node ['checked'] = $layer->status ? TRUE : FALSE;
			
			$tree [] = $node;
		}
		return json_encode ( $tree );
	}
}
?>