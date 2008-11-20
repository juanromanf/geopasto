<?php
/**
 * 
 * Esta clase maneja lo concerniente a la sesion de usuario
 * @package common
 *
 */
class AppSession {
	
	public function __construct() {
	}
	/**
	 * Permite crear la sesion del usuario
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
	 * Asigna el ID al usuario 
	 *
	 * @param array $user
	 */
	public static function setData($user) {
		
		$_SESSION ['USER_ID'] = $user->numide;
	}
	
	/**
	 * Retorna el ID del usuario logeado.
	 *
	 * @return int
	 */
	public static function getData() {
		
		return $_SESSION ['USER_ID'];
	}
	/**
	 * Finaliza la sesion del usuario
	 *
	 */
	public static function destroy() {
		
		session_destroy ();
	}
	/**
	 * Valida la sesion del usuario 
	 *
	 * @return bolean
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