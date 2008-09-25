<?php

class SII_Personas extends AppActiveRecord {
	public $_table = 'personas';
	
	public function getApellidos() {
		return $this->apellidos;
	}
	
	public function getCelular() {
		return $this->celular;
	}
	
	public function getDireccion() {
		return $this->direccion;
	}
	
	public function getNombres() {
		return $this->nombres;
	}
	
	public function getNumId() {
		return $this->numide;
	}
	
	public function getTelefono() {
		return $this->telefono;
	}
}
?>