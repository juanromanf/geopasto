<?php

class Config extends AppActiveRecord {
	public $_table = 'app_config';
	
	/**
	 * Retorna una instancia del objeto de Configuracion. 
	 *
	 * @return Config
	 */
	public static function _loadConfig() {
		$objConfig = new Config ( );
		$objConfig->Load ( 'id_conf = 1' );
		
		return $objConfig;
	}
	
	public function getServerName() {
		return $this->server_name;
	}
	
	public function getSesionTime() {		
		return $this->sesion_time;
	}
	
	public function getThemeName () {
		return $this->theme_name;
	}
	
	/**
	 * Retorna una cadena en formato JSON con los valores de la configuracion.
	 * Fomato cadena: [ id: 'campo', value: 'valor_campo']: 
	 *
	 * @return string
	 */
	public function getConfigArray() {
		
		try {
			$objConfig = Config::_loadConfig();
			
			$config = array (
			array ('id' => 'server_combo', 'value' => $objConfig->getServerName() ), 
			array ('id' => 'sesion_time', 'value' => $objConfig->getSesionTime() ), 
			array ('id' => 'theme_combo', 'value' => $objConfig->getThemeName()) 
			);
			
			return json_encode($config);
		
		} catch ( Exception $e ) {
			return NULL;
		}
	}
	
	/**
	 * Guarda los valores de la configuracion en la Base de Datos.
	 * $config['server_name'] - servidor de mapas a utilizar.
	 * $config['sesion_time'] - duracion de la sesion del usuario (min).
	 *
	 * @param array $frm_values
	 * @return xajaxResponse
	 */
	public function saveConfig($frm_values) {
		
		$config ['server_name'] = $frm_values ['server_name'];
		$config ['sesion_time'] = $frm_values ['sesion_time'];
		$config ['theme_name']  = $frm_values ['theme_name'];
		
		try {
			$objConfig = Config::_loadConfig();
			
			$objConfig->server_name = $config ['server_name'];
			$objConfig->sesion_time = $config ['sesion_time'];
			$objConfig->theme_name = $config ['theme_name'];
			$objConfig->Update ();
			
			$js = "Ext.MessageBox.alert('Globales', 'La configuracion ha sido guardada correctamente.', function(btn_id) { ConfigUI.closeTab(); } );";
		
		} catch ( Exception $e ) {
			$js = "Ext.MessageBox.alert('Globales', 'Hubo un error inesperado, por favor intentelo nuevamente.');";
		}
		
		$this->getXajaxResponse()->script($js);
	}
}
?>