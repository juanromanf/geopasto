<?php
	//-- Configuracion Sistema de Templates Smarty --
	class AppTemplate extends Smarty {

		function __construct() {
			$this->template_dir	= APP_DIR . 'templates';
			$this->compile_dir	= APP_DIR . 'templates_c';
			$this->config_dir	= APP_DIR . 'config';
			$this->cache_dir	= APP_DIR . 'cache';
			$this->left_delimiter = '|--';
			$this->right_delimiter = '--|';
		}
	}
?>