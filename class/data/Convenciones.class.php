<?php

class Convenciones extends AppActiveRecord {
	public $_table = 'app.convenciones';
	
	/**
	 * @return string
	 */
	public function getDisplay() {
		return $this->display;
	}
	
	/**
	 * @return integer
	 */
	public function getGid() {
		return $this->gid;
	}
	
	/**
	 * @return string
	 */
	public function getKeyvalue() {
		return $this->keyvalue;
	}
	
	/**
	 * @return string
	 */
	public function getLayer() {
		return $this->layer;
	}
	
	/**
	 * @return string
	 */
	public function getMap() {
		return $this->map;
	}
	
	/**
	 * @return string
	 */
	public function getOperator() {
		return $this->operator;
	}
	
	/**
	 * Return a Simbolos instance
	 *
	 * @return Simbolos
	 */
	public function getSymbol() {
		try {
			$sym = new Simbolos ( );
			$sym->Load ( "id_sym = " . $this->id_sym );
		
		} catch ( Exception $e ) {
			throw new Exception ( "Convenciones.getSymbol() - " . $e->getMessage () );
		}
		return $sym;
	}
	
	/**
	 * @param string $display
	 */
	public function setDisplay($display) {
		$this->display = $display;
	}
	
	/**
	 * @param string $keyvalue
	 */
	public function setKeyvalue($keyvalue) {
		$this->keyvalue = $keyvalue;
	}
	
	/**
	 * @param string $layer
	 */
	public function setLayer($layer) {
		$this->layer = $layer;
	}
	
	/**
	 * @param string $map
	 */
	public function setMap($map) {
		$this->map = $map;
	}
	
	/**
	 * @param string $operator
	 */
	public function setOperator($operator) {
		$this->operator = $operator;
	}
	
	public function toArray() {
		$array = parent::toArray();
		$array['detail'] = $this->getSymbol()->getDetail();
		
		return $array;
	}
	
	public function getAll($map = '%', $layer = '%', $asJson = false) {
		try {
			$arrayCon = $this->Find ( "map like '$map' and layer like '$layer' order by map, layer, keyvalue asc" );
			
			if ($asJson) {
				$json = array ();
				foreach ( $arrayCon as $convencion ) {
					$json [] = $convencion->toArray ();
				}
				
				return json_encode ( $json );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( "Convenciones.getAll() - " . $e->getMessage () );
		}
		return $arrayCon;
	}
	
	public function add($data) {
		try {
			$propertys = $data;
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$this->$key = $value;
			}
			$this->Insert ();
			
			$reload = "ConvencionesUI.reloadGrid();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( "ConvencionesUI.add() - " . $msg );
		}
	}
	
	public function remove($gid) {
		try {
			$this->Load ( 'gid = ' . $gid );
			$this->Delete ();
			
			$reload = "ConvencionesUI.reloadGrid();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'ConvencionesUI.remove() ' . $msg );
		}
	}
	
	public function modify($args) {
		try {
			$gid = $args [0]['value'];
			$propertys = $args;
			$this->Load ( "gid = $gid" );
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$this->$key = $value;
			}
			$this->Update ();
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'ConvencionesUI.modify() ' . $msg );
		}
	}

}
?>