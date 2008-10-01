<?php

class Perimetro extends AppActiveRecord {
	public $_table = 'gis.perimetro';
	
	public function getInfoXY($x, $y) {
		
		$info = array();
		$info[] = array ('seccion' => 'Perimetro', 'property' => 'Tipo', 'value' => 'Urbano' ); 
		return $info;
	}

}
?>