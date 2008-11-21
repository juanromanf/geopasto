<?php
/**
 * 
 * Clase para la administracion de las sesiones de los usuarios
 * del sistema.
 * 
 * @package common
 */
class AppSession {
	
	public function __construct() {
	}
	
	/**
	 * Inicializa la session del usuario.
	 *
	 */
	public static function startSession() {
		
		$sessionConfig = Config::getByKey ( 'session_time' );
		
		ini_set ( 'session.gc_divisor', '10' );
		ini_set ( 'session.gc_probability', '100' );
		ini_set ( 'session.gc_maxlifetime', $sessionConfig->value * 60 );
		
		session_start ();
	}
	
	/**
	 * Almacena el identificador del usuario que ingresa al sistema. 
	 *
	 * @param Usuarios $user instancia de la clase Usuarios.
	 */
	public static function setData($user) {
		
		$_SESSION ['USER_ID'] = $user->numide;
	}
	
	/**
	 * Retorna el identificador del usuario activo.
	 *
	 * @return int
	 */
	public static function getData() {
		
		return $_SESSION ['USER_ID'];
	}
	
	/**
	 * Finaliza la sesion del usuario.
	 *
	 */
	public static function destroy() {
		
		session_destroy ();
	}
	
	/**
	 * Veirifica la validez de la session del usuario dentro del sistema. 
	 *
	 * @return boolean
	 */
	public static function isValid() {
		
		if (isset ( $_SESSION ['USER_ID'] )) {
			return true;
		
		} else {
			return false;
		}
	}

}
?>