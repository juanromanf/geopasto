<?php
/*
Constantes configuracion de Directorios.
*/

$path = dirname ( __FILE__ );
$path = str_replace ( "\\", "/", $path );
define ( 'APP_DIR', $path . '/' );
define ( 'CLASS_DIR', APP_DIR . 'class/' );
define ( 'INCLUDE_DIR', APP_DIR . 'include/' );
define ( 'SMARTY_DIR', APP_DIR . 'include/smarty/libs/' );

require_once (INCLUDE_DIR . 'xajax/xajax_core/xajax.inc.php');

//-- Capa abstraccion de bases de datos ---
require (INCLUDE_DIR . 'adodb/adodb.inc.php');
require (INCLUDE_DIR . 'adodb/adodb-exceptions.inc.php');
require (INCLUDE_DIR . 'adodb/adodb-active-record.inc.php');

$ADODB_ASSOC_CASE = 0;

//-- Sistema de Templates ---
require (SMARTY_DIR . 'Smarty.class.php');

dl ( "php_mapscript." . PHP_SHLIB_SUFFIX );
date_default_timezone_set ( 'America/Bogota' );

define ( 'FPDF_FONTPATH', APP_DIR . 'include/fpdf/font/' );
require (INCLUDE_DIR . 'fpdf/fpdf.php');

/*
Funcion de autocarga de archivos de definicion de Clases.
Formato nombre archivo para que funcione: ClassName.class.php
*/
function __autoload($classname) {
	
	$arrayDirs = array (0 => "common", 1 => "ui", 2 => "data", 3 => "util" );
	
	foreach ( $arrayDirs as $dir ) {
		$class_file = CLASS_DIR . "$dir/$classname.class.php";
		
		if (file_exists ( $class_file )) {
			require_once ($class_file);
		}
	}
	if (file_exists ( APP_DIR . $classname . '.class.php' )) {
		require_once (APP_DIR . $classname . '.class.php');
	}
}

?>