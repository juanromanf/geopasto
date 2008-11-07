<?php

class Usuarios extends AppActiveRecord {
	public $_table = 'personas';
	
	private function Encriptar($cad) {
		$resultado = '';
		$cad = strrev ( $cad );
		$cad = base64_encode ( $cad );
		$n = floor ( strlen ( $cad ) / 4 );
		for($i = 0; $i < 3; $i ++) {
			$arreglo [$i] = substr ( $cad, $n * $i, $n );
		}
		$arreglo [3] = substr ( $cad, $n * 3, strlen ( $cad ) - $n * 3 );
		for($j = 4; $j > 0; $j --) {
			$cad = '';
			for($k = 0; $k < $j; $k ++) {
				$cad = $cad . $arreglo [$k];
			}
			$n = strlen ( $cad ) % $j;
			$resultado .= $arreglo [$n];
			$arreglo = $this->Descartar ( $arreglo, $n, $j - 1 );
		}
		return ($resultado);
	
	}
	
	private function Descartar($array, $n, $m) {
		for($i = $n; $i < $m; $i ++) {
			$array [$i] = $array [$i + 1];
			$array [$i + 1] = '';
		}
		return ($array);
	}
	
	private function desencriptar($cadena) {
		$n = floor ( strlen ( $cadena ) / 4 );
		$residuo = strlen ( $cadena ) % 4;
		for($i = 0; $i < 4; $i ++) {
			if ($n == $i) {
				$arreglo [$i] = substr ( $cadena, $n * $i, $n + $residuo );
			} else {
				if ($i > $n) {
					$arreglo [$i] = substr ( $cadena, $n * $i + $residuo, $n );
				} else {
					$arreglo [$i] = substr ( $cadena, $n * $i, $n );
				}
			}
		}
		for($j = 4; $j > 0; $j --) {
			//armamos la cadena
			$cadena = '';
			for($k = 0; $k < $j; $k ++) {
				$cadena = $cadena . $arreglo [$k];
			}
			$n = strlen ( $cadena ) % $j;
			$resultado .= $arreglo [$n];
			//descartarla
			$arreglo = $this->Descartar ( $arreglo, $n, $j - 1 );
		}
		$resultado = base64_decode ( $resultado );
		$resultado = strrev ( $resultado );
		
		return ($resultado);
	}
	//=================================================================================	
	public function doLogin($data) {
		try {
			$login = $data ['users_login'];
			$passwd = $this->Encriptar ( $data ['users_passwd'] );
			$ok = $this->Load ( "usuario = '$login' and r ='$passwd'" );
			
			if (! $ok) {
				$js = "Ext.getCmp('frmPanel').getEl().unmask();";
				$this->getXajaxResponse ()->script ( $js );
				throw new Exception("Compruebe su nombre de usuario y contrase&ntilde;a...");
				
			} else {
				AppSession::setData ( $this );
				$this->getXajaxResponse ()->redirect ( './' );
			}
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage() );
		}
	}
	
	public function doLogout() {
		
		AppSession::destroy ();
		$this->getXajaxResponse ()->redirect ( './' );
	}
	
	public static function getAllUsers($asJson = false) {
		try {
			$obj = new Usuarios ( );
			$result = $obj->Find ( 'activo = ' . 'true' . ' order by apellidos, nombres asc' );
			
			if ($asJson) {
				$root = array ();
				
				foreach ( $result as $user ) {
					$accordion = array ();
					$accordion ['numide'] = $user->numide;
					$accordion ['nombres'] = strtoupper ( $user->nombres );
					$accordion ['apellidos'] = strtoupper ( $user->apellidos );
					$accordion ['usuario'] = $user->usuario;
					$accordion ['activo'] = $user->activo;
					$accordion ['passwd'] = $user->desencriptar ( $user->r );
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
	}*/
/*
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
			 */
/*
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