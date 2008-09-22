<?php

class MapaTratamientosUI extends msMapLayout {
	protected $name = "mapa-tratamientos";
	protected $mapname = "tratamientos";
	
	public function createLayout($args) {
		
		$output = parent::createLayout ( $args );
		$layers = array ();
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Comunas', 'clsprefix' => 'Comuna ', 'clsdisplay' => 'num_comuna', 'clsitem' => 'num_comuna' );
		$layers [] = array ('map' => 'amenazas', 'layer' => 'Areas Homogeneas', 'clsprefix' => '', 'clsdisplay' => 'nombre', 'clsitem' => 'id_area' );
		$layers [] = array ('map' => 'tratamientos', 'layer' => 'Tratamientos', 'clsprefix' => '', 'clsdisplay' => 'descripcion', 'clsitem' => 'codtratamiento' );
		
		$this->addSymbols ( $layers );
		
		return $output;
	}
}
?>