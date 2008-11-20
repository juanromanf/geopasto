<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla p_pot_tipologiavias
 * 
 * @package data
 *
 */
class SII_PotTipologiaVias extends AppActiveRecord {
	public $_table = 'public.p_pot_tipologiavias';
	/**
	 * Retorna el nombre de la tipologia
	 * estipulad@s en el POT para el 
	 * municipio de Pasto
	 *
	 * @return String
	 */
	public function getNombreTipologia() {
		return $this->tipologia;
	}
}
?>