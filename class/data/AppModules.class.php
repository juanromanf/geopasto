<?php

class AppModules extends AppActiveRecord {
	public $_table = "app.modulos";
	
	/**
	 * Retorna todos los registros de la tabla como un array
	 * de objetos o como formato JSON.
	 *
	 * @param boolean $asJson
	 * @return AppModules
	 */
	public static function getAllModules($asJson = false) {
		try {
			$obj = new AppModules ( );
			$result = $obj->Find ( '1=1 order by position asc' );
			
			if ($asJson) {
				$root = array ();
				
				foreach ( $result as $module ) {
					$accordion = array ();
					$accordion ['id'] = $module->id_module;
					$accordion ['title'] = $module->title;
					$accordion ['iconcls'] = strlen ( $module->iconcls ) ? $module->iconcls : 'icon-16-emblem-generic';
					$accordion ['collapsed'] = $module->collapsed == 1 ? TRUE : FALSE;
					$accordion ['locked'] = $module->locked == 1 ? TRUE : FALSE;
					
					$root [] = $accordion;
				}
				return json_encode ( $root );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( 'AppModules.getAllModules() ' . $e->getMessage () );
		}
		return $result;
	}
	
	/**
	 * Inserta un nuevo registro en la tabla con los parametros
	 * enviados, el formato del array es:
	 * $args[] = { key: 'nombre del campo', value : 'valor del campo' }
	 * 
	 * @param array $args: { key: 'nombre del campo', value : 'valor del campo' }
	 */
	public function saveModule($args) {
		try {
			$propertys = $args;
			$obj = new AppModules ( );
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$obj->$key = $value;
			}
			
			$obj->Insert ();
			
			$reload = "MenuUI.reloadCombo();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'AppModules.saveModule() ' . $msg );
		}
	}
	
	/**
	 * Actualiza el registro en la tabla con los parametros
	 * enviados, el ID del item es necesario para la actualizacion.
	 * $args[] = { key: 'id', value : 'id del campo' }
	 * 
	 * El formato del array es:
	 * $args[] = { key: 'nombre del campo', value : 'valor del campo' }
	 *
	 * @param array $args: { key: 'id', value : 'id del campo' }
	 */
	public function updateModule($data) {
		try {
			$id = $data ['id'];
			$title = $data ['title'];
			$iconcls = $data ['iconcls'];
			$position = $data ['position'];
			
			$this->Load ( 'id_module = ' . $id );
			$this->title = $title;
			$this->iconcls = $iconcls;
			$this->position = $position;
			$this->Update ();
			
			$reload = "MenuUI.reloadCombo();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			throw new Exception ( "AppModule.updateModule() " . $e->getMessage () );
		}
	}
	
	/**
	 * Elimina el registro de la base de datos.
	 *
	 * @param int $id_module: Id del modulo a eliminar.
	 */
	public function deleteModule($id_modulo) {
		try {
			$this->Load ( 'id_module = ' . $id_modulo );
			/*
			 * Proteger integridad del sistema.
			 */
			if ($this->locked == 1) {
				throw new Exception ( "El Modulo '" . $this->title . "' no puede ser eliminado." );
			}
			$this->Delete ();
			
			$reload = "MenuUI.reloadCombo();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'AppModules.deleteModule() ' . $msg );
		}
	}
	
	/**
	 * Serializa los atributos del objeto a formato JSON
	 * Puede ser usado con otro registro enviando el ID del modulo.
	 *
	 * @param int $id_module: id del modulo, default NULL.
	 * @return string
	 */
	public function toJson($id_module = NULL) {
		
		if ($id_module != NULL) {
			/*
			 * Cargar el objeto con el ID proporcionado.
			 */
			$this->Load ( 'id_module = ' . $id_module );
		}
		/*
		 * Generar JSON con las propiedades actuales del objeto.
		 */
		$properties = array ();
		$properties ['id'] = $this->id_module;
		$properties ['title'] = $this->title;
		$properties ['iconcls'] = isset ( $this->iconcls ) ? $this->iconcls : 'icon-16-emblem-generic';
		$properties ['position'] = $this->position;
		$properties ['locked'] = $this->locked;
		
		return json_encode ( $properties );
	}
	
	/**
	 * Retorna todos los items correspondientes al modulo como un
	 * array de objetos o como formato JSON.
	 *
	 * @param boolean $asJson : retorno en formato JSON, default FALSE.
	 * @param int $id_module  : id del modulo.
	 * @return AppModuleMenus
	 */
	public function getMenus($asJson = false, $id_module = NULL) {
		try {
			
			$id = $id_module == NULL ? $this->id_module : $id_module;
			$obj = new AppModuleMenus ( );
			
			/* @var $module AppModules */
			$result = $obj->Find ( "id_module = $id order by position asc" );
			
			if ($asJson) {
				$root = array ();
				
				foreach ( $result as $item ) {
					$node = array ();
					
					$node ['id'] = $item->id_menu;
					$node ['id_module'] = $item->id_module;
					$node ['text'] = $item->text;
					$node ['leaf'] = $item->leaf == 1 ? TRUE : FALSE;
					$node ['iconcls'] = strlen ( $item->iconcls ) > 0 ? $item->iconcls : 'icon-16-preferences-system';
					$node ['action'] = $item->action;
					$node ['position'] = $item->position;
					
					$root [] = $node;
				}
				
				return json_encode ( $root );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( 'AppModules.getMenus() ' . $e->getMessage () );
		}
		return $result;
	}
	
	public function isAllowed($id_user) {
		$obj = new Permisos ( );
		$permisos = $obj->getRigths ( $id_user );
		
		foreach ( $permisos as $item ) {
			
			$menu = new AppModuleMenus ( );
			$menu->Load ( 'id_menu = ' . $item->id_menu );
			
			if ($menu->id_module == $this->id_module) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
?>