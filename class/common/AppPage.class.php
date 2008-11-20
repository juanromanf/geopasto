<?php

/**
 * 
 * Clase abstracta para el manejo de las diferentes
 * ventanas de la interfaz de la herramienta
 *
 * @package common
 */
abstract class AppPage {
	/**
	 * Nombre del Objeto.
	 *
	 * @var String
	 */
	protected $name = null;
	
	/**
	 * Motor de plantillas Smarty.
	 *
	 * @var AppTemplate
	 */
	protected $tpl = null;
	
	/**
	 * Objeto de manipulacion de AJAX.
	 *
	 * @var xajaxResponse
	 */
	private $xResponse = Null;
	
	public function __construct($xajaxResponse = null) {
		
		$this->tpl = new AppTemplate ( );
		$this->xResponse = $xajaxResponse;
	}
	
	/**
	 * Getter para propiedad xResponse.
	 *
	 * @return xajaxResponse
	 */
	public function getXajaxResponse() {
		return $this->xResponse;
	}
	
	/**
	 * Renderiza una platilla y retorna el HTML generado.
	 *
	 * @param string $template_file ruta archivo template.
	 * @param bool $output mostrar en pantalla el contenido de la plantilla.
	 * @return string contenido HTML de la plantilla.
	 */
	public function renderTemplate($template_file, $output = false) {
		
		$template_file = strtolower ( $this->name . '/' . $template_file );
		if ($output) {
			$this->tpl->display ( $template_file );
			return '';
		
		} else {
			return $this->tpl->fetch ( $template_file );
		}
	}
	/**
	 * Genera la plantilla index
	 *
	 * @param boolean $output
	 * @return string contenido HTML de la plantilla
	 */
	public function Index($output = false) {
		
		$template_file = 'index.html';
		return $this->renderTemplate ( $template_file, $output );
	
	}
	/**
	 * Genera la plantilla de Adicion
	 *
	 * @param boolean $output
	 * @return string contenido HTML de la plantilla
	 */
	public function DisplayAdd($output = false) {
		
		$template_file = 'add.html';
		return $this->renderTemplate ( $template_file, $output );
	}
	/**
	 * Genera la plantilla de Edicion
	 *
	 * @param boolean $output
	 * @return string contenido HTML de la plantilla
	 */
	public function DisplayEdit($output = false) {
		
		$template_file = 'edit.html';
		return $this->renderTemplate ( $template_file, $output );
	}
	
	/**
	 * Genera la plantilla de borrado
	 *
	 * @param boolean $output
	 * @return string contenido HTML de la plantilla
	 */
	public function DisplayDelete($output = false) {
		
		$template_file = 'delete.html';
		return $this->renderTemplate ( $template_file, $output );
	}
	
	/**
	 * Genera la plantilla para la vista
	 *
	 * @param boolean $output
	 * @return string contenido HTML de la plantilla
	 */
	public function DisplayView($output = false) {
		
		$template_file = 'view.html';
		return $this->renderTemplate ( $template_file, $output );
	}
}
?>