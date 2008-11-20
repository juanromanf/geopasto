<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla simbolos
 * 
 * @package data
 *
 */
class Simbolos extends AppActiveRecord {
	public $_table = 'app.simbolos';
	/**
	 * REtorna el detalle del simbolo
	 *
	 * @return String
	 */
	public function getDetail() {
		return $this->detail;
	}
	/**
	 * Retorna el nombre del Simbolo
	 *
	 * @return String
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * Retorna los datos de color del simbolo
	 *
	 * @return String
	 */
	public function getColor() {
		return $this->color;
	}
	/**
	 * Retorna los datos de color de la linea
	 *
	 * @return String
	 */
	public function getOutLineColor() {
		return $this->outlinecolor;
	}
	/**
	 * Retorna el tamano del simbolo
	 *
	 * @return String
	 */
	public function getSize() {
		return $this->size;
	}
	/**
	 * Retorna el Ancho del Simbolo
	 *
	 * @return String
	 */
	public function getWidth() {
		return $this->width;
	}
	/**
	 * Retorna todos los simbolos
	 * que estan registrados en el 
	 * sistema
	 *
	 * @param Bolean $asJson
	 * @return string JSON | array objetos Simbolos
	 */
	public static function getAll($asJson = false) {
		try {
			$obj = new Simbolos ( );
			$result = $obj->Find ( "1=1 order by detail asc" );
			
			if ($asJson) {
				$json = array ();
				foreach ( $result as $record ) {
					/* @var $record Simbolos */
					$json [] = $record->toArray ();
				}
				return json_encode ( $json );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( "Simbolos.getAll() - " . $e->getMessage () );
		}
		return $result;
	}
	/**
	 * Adiciona un simbolo
	 * al registro del sistema
	 *
	 * @param array $data
	 */
	public function addSym($data) {
		try {
			$propertys = $data;
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$this->$key = $value;
			}
			$this->Insert ();
			
			$reload = "SimbolosUI.reloadGrid();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( "Simbolos.addSym() - " . $msg );
		}
	}
	/**
	 * Borra un simbolo del 
	 * registro del sistema
	 *
	 * @param Integer $id_sym
	 */
	public function deleteSym($id_sym) {
		try {
			$this->Load ( 'id_sym = ' . $id_sym );
			$this->Delete ();
			
			$reload = "SimbolosUI.reloadGrid();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'Simbolos.deleteSym() ' . $msg );
		}
	}
	/**
	 * Actualiza los datos 
	 * de los simbolos registrados
	 * en el sistema
	 *
	 * @param array $args
	 */
	public function updateSym($args) {
		try {
			$id_sym = $args [0]['value'];
			$propertys = $args;
			$this->Load ( 'id_sym = ' . $id_sym );
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$this->$key = $value;
			}
			$this->Update ();
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'Simbolos.updateSym() ' . $msg );
		}
	}

}
?>