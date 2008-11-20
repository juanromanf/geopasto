<?php
/**
 * 
 * Clase encargada del manejo de los datos
 * de la tabla seguridad
 * 
 * @package data
 *
 */
class Permisos extends AppActiveRecord {
	public $_table = 'app.seguridad';
	
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
			$permisos = $this->Find ( "numide = $id_user" );
			/* @var $revoke Permisos */
			foreach ( $permisos as $revoke ) {
				$where = "numide = ". $revoke->numide . " and id_menu = ". $revoke->id_menu;
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
	 * @param int $numide: id del usuario.
	 * @param array $arrayPermisos: id's de los menus permitidos al usuario.
	 */
	public function setRigth($numide, $arrayPermisos) {
		
		try {
			$this->revomeAll ( $numide );
			foreach ( $arrayPermisos as $idmenu ) {
				$this->numide = $numide;
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
	 * @param int $numide: id del usuario
	 * @param bool $asJson: retornar el resultado en formato JSON.
	 * @return string|array
	 */
	public function getRigths($numide, $asJson = false) {
		
		try {
			$arrayPermisos = $this->Find ( "numide = $numide" );
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