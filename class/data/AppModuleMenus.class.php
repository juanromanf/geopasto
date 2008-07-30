<?php

class AppModuleMenus extends AppActiveRecord {
	public $_table = "app_module_menus";
	
	public function isAllowed($id_user) {
		$obj = new Permisos();
		$permisos = $obj->Find("id_user = $id_user and id_menu = ". $this->id_menu);
		
		if (count($permisos) > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * Inserta un nuevo registro en la tabla con los parametros
	 * enviados, el formato del array es:
	 * $args[] = { key: 'nombre del campo', value : 'valor del campo' }
	 * 
	 * @param array $args
	 */
	public function saveItem($args) {
		try {
			$propertys = $args;
			$obj = new AppModuleMenus ( );
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$obj->$key = $value;
			}
			
			$obj->Insert ();
			
			$reloadgrid = "MenuUI.fireComboSelect();";
			$this->getXajaxResponse ()->script ( $reloadgrid );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( $msg );
		}
	}
	
	/**
	 * Actualiza el registro en la tabla con los parametros
	 * enviados.
	 * 
	 * El ID del item es necesario para la actualizacion.
	 * $args[] = { key: 'id', value : 'id del campo' }
	 * 
	 * El formato del array es:
	 * $args[] = { key: 'nombre del campo', value : 'valor del campo' }
	 *
	 * @param array $args
	 */
	public function updateItem($args) {
		try {
			$propertys = $args;
			$id_item = '';
			
			foreach ( $args as $p ) {
				$key = $p ['key'];
				if ($key == 'id') {
					$id_item = $p ['value'];
				}
			}
			
			$obj = new AppModuleMenus ( );
			$obj->Load ( 'id_menu = ' . $id_item );
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$obj->$key = $value;
			}
			$obj->Update ();
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( $msg );
		}
	}
	
	/**
	 * Elimina el registro de la base de datos.
	 *
	 * @param int $id_item: Id del item ha eliminar.
	 */
	public function deleteItem($id_item) {
		try {
			$obj = new AppModuleMenus ( );
			$obj->Load ( 'id_menu = ' . $id_item );
			
			/*
			 * Proteger integridad del sistema.
			 */
			if ($obj->locked == 1) {
				throw new Exception ( "El item '" . $obj->text . "' no puede ser eliminado." );
			}
			$obj->Delete ();
			
			$reloadgrid = "MenuUI.fireComboSelect();";
			$this->getXajaxResponse ()->script ( $reloadgrid );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( $msg );
		}
	}
}
?>