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
}
?>