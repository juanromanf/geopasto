<?php
/**
 * 
 * Clase responsable de la construccion de la interfaz 
 * de administracion de usuarios del sistema.
 * 
 * @package ui
 */
class UsuariosUI extends AppPage {

	protected $name = 'Usuarios';
	/**
	 * 
	 * Renderiza la plantilla para la contruccion de la interfaz
	 * de ingreso al sistema. 
	 * 
	 * @return string HTML con la composicion del formulario de ingreso al sistema.
	 */
	public function displayLogin() {
		$template = 'login.html';
		return $this->renderTemplate($template);
	}
}
?>