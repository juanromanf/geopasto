<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla personas que se encuentren 
 * registradas en el sistema
 * 
 * @package data
 *
 */
class SII_Personas extends AppActiveRecord {
	public $_table = 'personas';
	/**
	 * Retorna los apellidos del usuario
	 *
	 * @return String
	 */
	public function getApellidos() {
		return $this->apellidos;
	}
	/**
	 * Retorna el numero de celular del usuario
	 *
	 * @return String
	 */
	public function getCelular() {
		return $this->celular;
	}
	/**
	 * Retorna la direccion del usuario
	 *
	 * @return String
	 */
	public function getDireccion() {
		return $this->direccion;
	}
	/**
	 * Retorna los nombres del Usuario
	 *
	 * @return String
	 */
	public function getNombres() {
		return $this->nombres;
	}
	/**
	 * Retorna el numero de identificacion del usuario
	 *
	 * @return String
	 */
	public function getNumId() {
		return $this->numide;
	}
	/**
	 * Retorna el numero de telefono del usuario
	 *
	 * @return String
	 */
	public function getTelefono() {
		return $this->telefono;
	}
}
?>