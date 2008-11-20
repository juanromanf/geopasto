<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_areasactividad
 * 
 * @package data
 *
 */
class SII_PotAreasActividad extends AppActiveRecord {
	public $_table = 'public.p_pot_areasactividad';
	/**
	 * Retorna todas las Areas de Actividad
	 * estipuladas en el POT para el 
	 * municipio de Pasto
	 * Retornan como un array de objetos o como formato JSON.
	 * 
	 * @param Bolean $asJson default false
	 * @return string JSON | array objetos SII_PotAreasActividad
	 */
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
	/**
	 * Retorna el nombre del Area
	 *
	 * @return String
	 */
	public function getNombreArea() {
		return $this->areaactividad;
	}
	/**
	 * Retorna la sigla de identificacion
	 *
	 * @return String
	 */
	public function getSigla() {
		return $this->sigla;
	}
}
?>