<?php

class AppSession {

	public function __construct() {
	}

	public static function startSession() {
		
		$objConfig = Config::_loadConfig();

		ini_set('session.gc_divisor', '10');
		ini_set('session.gc_probability', '100');
		ini_set('session.gc_maxlifetime', $objConfig->getSesionTime() * 60);

		session_start();
	}

	public static function setData($user) {

		$_SESSION['USER_ID'] = $user->id_user;
	}

	/**
	 * Retorna el ID del usuario logeado.
	 *
	 * @return int
	 */
	public static function getData() {

		return $_SESSION['USER_ID'];
	}

	public static function destroy() {

		session_destroy();
	}

	public static function isValid() {

		if ( isset($_SESSION['USER_ID'])) {
			return true;

		} else {
			return false;
		}
	}

}
?>