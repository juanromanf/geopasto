<?php
/**
 * 
 * Es una clase que implementa el patron de 
 * AppPage para mostrar la pagina de Inicio de Sesion
 * 
 * @package ui
 * 
 */
class UsuariosUI extends AppPage {

	protected $name = 'Usuarios';
	/**
	 * 
	 * Crea la interfaz de usuario 
	 * para realizar el ingreso al sistema
	 * 
	 * @return String que contiene el HTML
	 */
	public function displayLogin() {
		$template = 'login.html';
		return $this->renderTemplate($template);
	}
}
?>