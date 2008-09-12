<?php

class MapaAmenazasUI extends msMapLayout {
	protected $name = "mapa-amenazas";
	
	public function createLayout($args) {
		
		$output = parent::createLayout ( $args );
		$layers = array ();
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Comunas', 'clsprefix' => 'Comuna ', 'clsdisplay' => 'num_comuna', 'clsitem' => 'num_comuna' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Areas Homogeneas', 'clsprefix' => '', 'clsdisplay' => 'nombre', 'clsitem' => 'id_area' );
		
		$this->addSymbols ( $layers );
		
		return $output;
	}
}
?>