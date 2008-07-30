<?php

class AppMenu {
	
	public function __construct() {
	
	}
	
	public function getModules() {
		
		try {
			$result = AppModules::getAllModules ();
			
			$root = array ( );
			
			foreach ( $result as $module ) {
				/* @var $module AppModules */
				if ($module->isAllowed ( AppSession::getData () )) {
					$accordion = array ( );
					
					$accordion ['id'] = 'panel-' . $module->id_module;
					$accordion ['title'] = $module->title;
					$accordion ['iconCls'] = strlen ( $module->iconcls ) ? $module->iconcls : 'icon-16-emblem-generic';
					$accordion ['collapsed'] = $module->collapsed == 1 ? TRUE : FALSE;
					$accordion ['border'] = FALSE;
					
					$root [] = $accordion;
				}
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( 'Error en AppMenu.getModules(), no se logro construir los modulos.' );
		}
		return json_encode ( $root );
	}
	
	public function getMenuTree($id_module) {
		try {
			list ( $prefix, $id ) = explode ( '-', $id_module );
			
			$objModule = new AppModules ( );
			$objModule->Load ( "id_module = $id" );
			$menus = $objModule->getMenus ();
			
			foreach ( $menus as $item ) {
				/* @var $item AppModuleMenus */
				if ($item->isAllowed ( AppSession::getData () )) {
					$node = array ( );
					
					$node ['id'] = "node-" . $item->id_menu;
					$node ['text'] = $item->text;
					$node ['iconCls'] = strlen ( $item->iconcls ) > 0 ? $item->iconcls : 'icon-16-preferences-system';
					$node ['action'] = $item->action;
					$node ['leaf'] = $item->leaf == 1 ? TRUE : FALSE;
					
					$root [] = $node;
				}
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( 'Error en AppMenu.getMenuTree(), no se logro construir el arbol del menu. (' . $e->getMessage () . ')' );
		}
		return json_encode ( $root );
	}
}
?>