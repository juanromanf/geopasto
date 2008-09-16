<?php

class AppSession {

	public function __construct() {
	}

	public static function startSession() {
		
		$sessionConfig = Config::getByKey('session_time');

		ini_set('session.gc_divisor', '10');
		ini_set('session.gc_probability', '100');
		ini_set('session.gc_maxlifetime', $sessionConfig->value * 60);

		session_start();
	}

	public static function setData($user) {

		$_SESSION['USER_ID'] = $user->numide;
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