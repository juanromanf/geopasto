<?php

class SII_Predios extends AppActiveRecord {
	public $_table = 'public.predios';
	
	/**
	 * Retorna instacia de la Persona propietaria del predio.
	 *
	 * @return SII_Personas
	 */
	public function getPropietario() {
		try {
			$propietario = new SII_Propietarios ( );
			$propietario->Load ( "numpredio = '" . $this->numpredio . "'" );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $propietario->getPersona ();
	}
	
	private function getUltimaSolicitud($tiposolicitud = 1) {
		try {
			$sql = "SELECT max(s1.codsolicitud) FROM p_pot_solicitudes s1 
					WHERE s1.numpredio = '$this->numpredio' AND
						  s1.tiposolicitud = '$tiposolicitud'";
			$db = AppSQL::getInstance ();
			$rs = $db->Execute ( $sql );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $rs->fields [0];
	}
	
	public function getDireccion() {
		return $this->direccion;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_Estratos
	 */
	public function getDemEstrato() {
		try {
			$solicitud = $this->getUltimaSolicitud ();
			$e = new SII_Estratos ( );
			$e->Load ( "codsolicitud = '$solicitud'" );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		return $e;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_Barrios
	 */
	public function getBarrio() {
		try {
			$barrio = new SII_Barrios ( );
			$barrio->Load ( "codbarrio = '" . $this->codbarrio . "'" );
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		return $barrio;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotDemarcaciones
	 */
	public function getDemarcion() {
		$solicitud = $this->getUltimaSolicitud ();
		$demarcacion = new SII_PotDemarcaciones ( );
		$demarcacion->Load ( "codsolicitud = '$solicitud' and numpredio = '" . $this->numpredio . "'" );
		
		return $demarcacion;
	}
	
	public function getAmenazas() {
		$obj = new SII_AmenazasPredios ( );
		$rs = $obj->Find ( "numpredio = '" . $this->numpredio . "'" );
		
		$amenazas = array ();
		foreach ( $rs as $r ) {
			$a = new SII_PotAmenazas ( );
			$a->Load ( "codamenaza = '" . $r->codamenaza . "'" );
			$amenazas [] = $a;
		}
		return $amenazas;
	}
}
?>