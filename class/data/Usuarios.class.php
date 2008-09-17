<?php

class Usuarios extends AppActiveRecord {
	public $_table = 'personas';
	
	public function doLogin($data) {
		$login = $data ['users_login'];
		$passwd = $data ['users_passwd'];
		$ok = $this->Load ( "usuario = '$login' and r ='$passwd'" );
		if (! $ok) {
			$js = "Ext.MessageBox.alert('Error','Compruebe su usuario y contrase&ntilde;a...');";
			$this->getXajaxResponse ()->script ( $js );
		} else {
			AppSession::setData ( $this );
			$this->getXajaxResponse ()->redirect ( './' );
		}
	}
	
	public function doLogout() {
		
		AppSession::destroy ();
		$this->getXajaxResponse ()->redirect ( './' );
	}
	
	public static function getAllUsers($asJson = false) {
		try {
			$obj = new Usuarios ( );
			$result = $obj->Find ( 'activo = '. 'true' .' order by apellidos, nombres asc' );
			
			if ($asJson) {
				$root = array ();
				
				foreach ( $result as $user ) {
					$accordion = array ();
					$accordion ['numide'] = $user->numide;
					$accordion ['nombres'] = $user->nombres;
					$accordion ['apellidos'] = $user->apellidos;
					$accordion ['usuario'] = $user->usuario;
					$accordion ['activo'] = $user->activo;
					$root [] = $accordion;
				}
				return json_encode ( $root );
			}
		
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
		
		return $result;
	}
	/*
	public function updateUser($args) {
		try {
			$propertys = $args;
			$id_user = '';
			$passwd = '';
			
			foreach ( $args as $p ) {
				$key = $p ['key'];
				if ($key == 'id') {
					$id_user = $p ['value'];
				}
				if ($key == 'passwd') {
					$passwd = $p ['value'];
				}
			}
			
			$this->Load ( 'id_user = ' . $id_user );
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$this->$key = $value;
			}
			if ($passwd != '') {
				$this->passwd = md5 ( $passwd );
			}
			
			$this->modified = AppSQL::getInstance ()->BindDate ( date ( 'Y-m-d' ) );
			$this->Update ();
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( $msg );
		}
	}
	
	public function addUser($data) {
		try {
			$propertys = $data;
			foreach ( $data as $p ) {
				$key = $p ['key'];
				if ($key == 'passwd') {
					$passwd = $p ['value'];
				}
			}
			
			foreach ( $propertys as $p ) {
				$key = $p ['key'];
				$value = $p ['value'];
				$this->$key = $value;
			}
			$this->passwd = md5 ( $passwd );
			$this->created = AppSQL::getInstance ()->BindDate ( date ( 'Y-m-d' ) );
			$this->modified = AppSQL::getInstance ()->BindDate ( date ( 'Y-m-d' ) );
			
			$this->Insert ();
			
			$reload = "UsuariosUI.reloadUsers();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( $msg );
		}
	
	}
	
	public function deleteUser($data) {
		try {
			$this->Load ( 'id_user = ' . $data );
			/*
			 * Proteger integridad del sistema.
			 *//*
			if ($this->locked == 1) {
				throw new Exception ( "El Usuario '" . $this->name . "' no puede ser eliminado." );
			}
			$this->Delete ();
			
			$reload = "UsuariosUI.reloadUsers();";
			$this->getXajaxResponse ()->script ( $reload );
		
		} catch ( Exception $e ) {
			$msg = $e->getMessage ();
			throw new Exception ( 'Usuarios.deleteUser() ' . $msg );
		}
	}*/
}
?>