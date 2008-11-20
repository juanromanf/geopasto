<?php
/**
 * @package data
 *
 */
class SpatialRefSys extends AppActiveRecord {
	public $_table = 'spatial_ref_sys';
	/**
	 * REtorna el nombre del
	 * tipo de proyeccion
	 *
	 * @return String
	 */
	public function getProjectionName() {
		$pos1 = strpos ( $this->srtext, '"' );
		$pos2 = strpos ( $this->srtext, '"', $pos1 + 1 );
		
		return substr ( $this->srtext, $pos1 + 1, $pos2 - $pos1 - 1 );
	}
	/**
	 * Evalua las proyecciones 
	 *
	 * @return objeto SpatialRefSys
	 */
	public static function getAvailableProjections() {
		$spatialObj = new SpatialRefSys ( );
		$spatialArray = $spatialObj->Find ( "1=1 order by srtext asc" );
		
		$projArray = array ( );
		foreach ( $spatialArray as $proj ) {
			if (strpos ( $proj->srtext, "(deprecated)" ) === false) {
				$projArray [] = $proj;
			}
		}
		
		return $projArray;
	}
}
?>