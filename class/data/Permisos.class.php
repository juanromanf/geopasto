<?php

class Permisos extends AppActiveRecord {
	public $_table = 'app_security';
	
	/**
	 * funcion para armar el arbol de permisos.
	 *
	 * @return String
	 */
	public function getSecurityTree() {
		$arraymodulos = AppModules::getAllModules ();
		$root = array ();
		$root ['text'] = "padre";
		$root ['children'] = array ();
		
		/* @var $modulo AppModules */
		foreach ( $arraymodulos as $modulo ) {
			
			$p_modulo = array ();
			$p_modulo ['id_modulo'] = $modulo->id_module;
			$p_modulo ['text'] = $modulo->title;
			$p_modulo ['expanded'] = TRUE;
			$p_modulo ['children'] = array ();
			$arraymenus = $modulo->getMenus ();
			
			foreach ( $arraymenus as $menu ) {
				$p_item = array ();
				$p_item ['id'] = $menu->id_menu;
				$p_item ['text'] = $menu->text;
				$p_item ['iconCls'] = $menu->iconcls;
				$p_item ['leaf'] = TRUE;
				$p_item ['checked'] = FALSE;
				$p_modulo ['children'] [] = $p_item;
			}
			$root ['children'] [] = $p_modulo;
		}
		return json_encode ( $root );
	}
	
	/**
	 * Revoca todos los permisos del usuario.
	 *
	 * @param int $id_user: id del usuario.
	 */
	public function revomeAll($id_user) {
		try {
			$permisos = $this->Find ( "id_user = $id_user" );
			/* @var $revoke Permisos */
			foreach ( $permisos as $revoke ) {
				$where = "id_user = ". $revoke->id_user . " and id_menu = ". $revoke->id_menu;
				$this->Load($where);
				$this->Delete();
			}
		} catch ( Exception $e ) {
			throw new Exception ( 'Permisos.removeAll() ' . $e->getMessage () );
		}
	}
	
	/**
	 * Asigna los permisos al usuario.
	 *
	 * @param int $id_user: id del usuario.
	 * @param array $arrayPermisos: id's de los menus permitidos al usuario.
	 */
	public function setRigth($id_user, $arrayPermisos) {
		
		try {
			$this->revomeAll ( $id_user );
			foreach ( $arrayPermisos as $idmenu ) {
				$this->id_user = $id_user;
				$this->id_menu = $idmenu;
				$this->Insert ();
			}
			$js = "PermisosUI.closewindow();";
			$this->getXajaxResponse ()->script ( $js );
		
		} catch ( Exception $e ) {
			throw new Exception ( 'Permisos.setRigth() ' . $e->getMessage () );
		}
	}
	
	/**
	 * Recupera los permisos asignados a un usuario.
	 *
	 * @param int $id_user: id del usuario
	 * @param bool $asJson: retornar el resultado en formato JSON.
	 * @return string|array
	 */
	public function getRigths($id_user, $asJson = false) {
		
		try {
			$arrayPermisos = $this->Find ( "id_user = $id_user" );
			if ($asJson) {
				$arrayJson = array ();
				foreach ( $arrayPermisos as $permiso ) {
					$item = array ();
					$item ['id_menu'] = $permiso->id_menu;
					$arrayJson [] = $item;
				}
				return json_encode ( $arrayJson );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( 'Permisos.getRigths() ' . $e->getMessage () );
		}
		return $arrayPermisos;
	}
}
?>