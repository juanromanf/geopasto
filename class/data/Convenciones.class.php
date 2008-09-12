<?php

class Convenciones extends AppActiveRecord {
	public $_table = 'symbols.convenciones';
	
	public function getAll($map, $layer) {
		try {
			$arraySym = $this->Find ( "map = '$map' and layer = '$layer' order by keyvalue asc" );
			
		} catch ( Exception $e ) {
			throw new Exception ( "Convenciones.getAll() - " . $e->getMessage () );
		}
		return $arraySym;
	}
	
	/**
	 * Return a Simbolos instance
	 *
	 * @return Simbolos
	 */
	public function getSymbol() {
		try {
			$sym = new Simbolos ( );
			$sym->Load("id_sym = ". $this->id_sym);
			
		} catch ( Exception $e ) {
			throw new Exception ( "Convenciones.getSymbol() - " . $e->getMessage () );
		}
		return $sym;
	}
}
?>