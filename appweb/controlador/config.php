<?php

// Posibilidades para deshabilitar el caché según varios tipos de navegadores. ¿**Deberían ir en el controlador**?
header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Constantes propias de la aplicación
define('COMSPEC', filter_input(INPUT_SERVER, 'COMSPEC'));
define('ROOT', filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'));
define('DOCUMENT_ROOT', substr(ROOT, -1) == '/' ? ROOT : ROOT . '/');
define('RUTA_APLICACION', '../');
define('PHPEXCEL_ROOT', '../../includes/PHPExcel/');
define('RUTA_DESCARGA', '../serviciosTecnicos/varios/');
//-----------------
define('PHPWORD_ROOT', '../../includes/PHPWord/');
define('TMP_PATH', sys_get_temp_dir() . DIRECTORY_SEPARATOR);

// Atributos de la conexión a la base de datos (remota)
define('BASE_DATOS', 'usi');
define('SERVIDOR', 'phpma.dijansoft.xyz');
define('PUERTO', '5432');
define('USUARIO', 'usiuser');//postgres
define('CONTRASENA', 'usiapp');//123456

//conexion a BD local
/*define('BASE_DATOS', 'Proyecto_USI');
define('SERVIDOR', 'localhost');
define('PUERTO', '5432');
define('USUARIO', 'postgres');//postgres
define('CONTRASENA', 'abc123');//123456*/ 	


spl_autoload_register('__autoload');
// Para PHP 6 E_STRICT es parte de E_ALL -- error_reporting(E_ALL | E_STRICT); para verificación exhaustivo --
error_reporting(E_ERROR);

/**
 * Intenta cargar una clase siguiendo la siguiente convención:
 * Si el nombre de la clase comienza con Util, la clase será una clase de utilidades con 
 * métodos estáticos que se cargada desde la carpeta "Utilidades", en caso contrario se
 * cargará desde la carpeta "Modelo" y no definirá métodos estáticos
 * @param type $nombreClase El nombre de la clase a cargar
 */
function __autoload($nombreClase) {

    if (substr($nombreClase, 0, 7) == 'Reporte') {
        $nombreClase = "../serviciosTecnicos/reportes/$nombreClase.php";
    } else if (substr($nombreClase, 0, 4) == 'Util') {
        $nombreClase = "../serviciosTecnicos/utilidades/$nombreClase.php";
    } else if (substr($nombreClase, 0, 8) == 'PHPExcel') {
        $nombreClase = PHPEXCEL_ROOT . str_replace('_', '/', $nombreClase) . '.php';
    } else if (substr($nombreClase, 0, 7) == 'PHPWord') {
        $nombreClase = PHPWORD_ROOT . str_replace('_', '/', $nombreClase) . '.php';
    } else {
        $nombreClase = "../modelo/$nombreClase.php";
    }
    include_once($nombreClase);
}
