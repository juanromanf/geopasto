<?php

class AppHome extends AppPage {
	
	protected $name = 'App';
	
	/**
	 * Class constructor.
	 *
	 * @param boolean $register
	 */
	public function __construct($register = false) {
		parent::__construct ();
		
		if ($register) {
			$xajax = new xajax ( );
			$xajax->configure ( 'waitCursor', false );
			//$xajax->configure ( 'debug', true );			

			/**
			 * 	Register all Classes needed.
			 */
			$arrayClasses = array ('AppHome' );
			
			foreach ( $arrayClasses as $class_name ) {
				$xajax->register ( XAJAX_CALLABLE_OBJECT, new $class_name ( ) );
			}
			
			$xajax->processRequest ();
			$config = Config::_loadConfig ();
			
			$this->tpl->assign ( 'theme_name', $config->getThemeName () );
			$this->tpl->assign ( 'xajax_js', $xajax->getJavascript ( INCLUDE_DIR . 'xajax/' ) );
		}
		
		$this->tpl->assign ( 'page_title', '.:: Pasto - Sistema de Informacion Geografica ::.' );
	}
	
	/**
	 * Render Application layout (main page).
	 *
	 * @param boolean $output Catch result or show into browser.
	 * @return string HTML content.
	 */
	public function DisplayLayout($output) {
		$template_file = 'layout.html';
		return $this->renderTemplate ( $template_file, $output );
	}
	
	public function DisplayWelcome() {
		$template_file = 'welcome.html';
		
		$objusr = new Usuarios ( );
		$objusr->Load ( 'id_user=' . AppSession::getData () );
		$this->tpl->assign ( 'user_name', $objusr->name );
		$this->tpl->assign ( 'user_time', date('h:i a') );
		
		return $this->renderTemplate ( $template_file );
	}
	
	/**
	 * Method to validate if action (Class and method) exist. 
	 *
	 * @param string $action
	 * @return boolean
	 */
	private static function isValidAction($action) {
		list ( $class, $method ) = explode ( '.', $action );
		
		if (class_exists ( $class )) {
			$arrayMethods = get_class_methods ( $class );
			
			if (in_array ( $method, $arrayMethods )) {
				//				return true;
			} else {
				throw new Exception ( "El metodo espeficidado no existe. ($action)" );
			}
		} else {
			throw new Exception ( "La clase espeficidada no existe. ($action)" );
		}
	}
	
	/**
	 *	Ejecuta una accion espeficada con los siguientes parametros:
	 * 	$params[action]		= Accion a ejecutar 'Clase.Metodo'.
	 *  $params[target]		= Id del elemento HTML a actualizar mediante ajax.
	 * 	$params[property]	= Propiedad del elemento a actualizar.
	 *	$params[args]		= Argumentos de la accion a ejecutar.
	 * 	$params[returnvalue]= TRUE si se desea que la accion retorne un valor (para llamada sincronas).
	 *  $params[enableajax]	= TRUE si se desea habilitar AJAX para la accion.
	 *
	 * @param array $params
	 * @return xajaxResponse
	 */
	public function exec($params = null) {
		$objResponse = new xajaxResponse ( );
		
		try {
			/*
			 * Gestion de parametros.
			 */
			$action = $params ['action'];
			$target = isset ( $params ['target'] ) ? $params ['target'] : 'debug-div';
			$property = isset ( $params ['property'] ) ? $params ['property'] : 'innerHTML';
			$args = isset ( $params ['args'] ) ? $params ['args'] : NULL;
			$jsCallback = isset ( $params ['jscallback'] ) ? $params ['jscallback'] : '';
			$return = isset ( $params ['returnvalue'] ) ? TRUE : FALSE;
			$ajax = isset ( $params ['enableajax'] ) ? TRUE : FALSE;
			
			/*
			 * Verificar validez de la session para todas las 
			 * acciones, menos las del array $allowActions.
			 */
			$allowActions = array ('UsuariosUI.displayLogin', 'Usuarios.doLogin', 'Usuarios.doLogout' );
			
			if (! in_array ( $action, $allowActions )) {
				
				if (! AppSession::isValid ()) {
					
					$action = 'UsuariosUI.displayLogin';
					$target = 'layout-body';
					$jsCallback = 'UsuariosUI.init();';
					$ajax = TRUE;
					
					$objResponse->redirect ( './' );					
					return $objResponse;
				}
			}
			
			/*
			 * Verificar que se haya especificado una accion a ejecutar,
			 * y ademas que sea valida.
			 */
			if (strlen ( $action ) > 0) {
				list ( $class, $method ) = explode ( '.', $action );
				AppHome::isValidAction ( $action );
			
			} else {
				throw new Exception ( 'No ha espeficidado ninguna accion.' );
			}
			
			/**
			 * 	Si existe el archivo con el nombre 'NombreClase.js',
			 *  cargar dinamicamente para su uso.
			 */
			$js_file = APP_DIR . 'js/' . strtolower ( $class ) . '.js';
			
			if (file_exists ( $js_file )) {
				$objResponse->includeScriptOnce ( $js_file );
				$objResponse->waitFor ( "typeof($class) != 'undefined'", 10 );
			}
			
			/*
			 * Instanciar la clase y enviar el Objeto xajaxResponse
			 * si se necesita.
			 */
			if ($ajax) {
				$obj = new $class ( $objResponse );
			
			} else {
				$obj = new $class ( );
			}
			
			/**
			 * 	Ejecutar clase y metodo con los parametros especificados.
			 */
			$output = call_user_func_array ( array ($obj, $method ), $args );
			
			/*
			 * Retornar un valor o realizar la actualizacion del elemento.
			 */
			if ($return) {
				$objResponse->setReturnValue ( $output );
			
			} else {
				$objResponse->assign ( $target, $property, $output );
			}
			
			/*
			 * Ejecutar Javascript adicional.
			 */
			$objResponse->script ( $jsCallback );
		
		} catch ( Exception $e ) {
			/**
			 * 	Generacion del log de errores del sistema.
			 */
			$target = "error-div";
			$this->tpl->assign ( 'error_date', date ( 'Y-m-d h:i:s a' ) );
			$this->tpl->assign ( 'error_msg', addslashes ( $e->getMessage () ) );
			$output = $this->tpl->fetch ( 'app.error.html' );
			$objResponse->prepend ( $target, $property, $output );
		}
		
		return $objResponse;
	}
}
?>