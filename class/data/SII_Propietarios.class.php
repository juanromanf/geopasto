<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla propietariospredios
 * 
 * @package data
 *
 */
class SII_Propietarios extends AppActiveRecord {
	public $_table = 'public.propietariospredios';
	
	
	/**
	 * Retorna una instacia de Persona.
	 *
	 * @return SII_Persona
	 */
	public function getPersona() {
		$p = new SII_Personas();
		$p->Load("numide = '$this->numidepropietario'");
		
		return $p;
	}
}
?>