<?php

class Simbolos extends AppActiveRecord {
	public $_table = 'app.simbolos';
	
	public function getDetail() {
		return $this->detail;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getColor() {
		return $this->color;
	}
	
	public function getOutLineColor() {
		return $this->outlinecolor;
	}
	
	public function getSize() {
		return $this->size;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
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