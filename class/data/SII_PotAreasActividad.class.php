<?php

class SII_PotAreasActividad extends AppActiveRecord {
	public $_table = 'public.p_pot_areasactividad';
	
	public static function getAll($asJson = false) {
		$obj = new SII_PotAreasActividad ( );
		$rs = $obj->Find ( '1 = 1 order by areaactividad' );
		
		if ($asJson) {
			$json = array ();
			foreach ( $rs as $r ) {
				$item = array ();
				$item ['codareaactividad'] = $r->codareaactividad;
				$item ['areaactividad'] = "(" . $r->sigla . ") " . htmlentities ( strtoupper ( $r->areaactividad ) );
				
				$json [] = $item;
			}
			
			return json_encode ( $json );
		}
		return $rs;
	}
	
	public function getNombreArea() {
		return $this->areaactividad;
	}
	
	public function getSigla() {
		return $this->sigla;
	}
}
?>