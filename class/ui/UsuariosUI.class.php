<?php

class UsuariosUI extends AppPage {

	protected $name = 'Usuarios';
	
	public function displayLogin() {
		$template = 'login.html';
		return $this->renderTemplate($template);
	}
}
?>