<?php

// La POO permite crear objetos anónimos, como este que se encarga de gestionar peticiones de la capa de presentación
// Este demo se probó con versiones PHP 5.5 o superiores
new Fachada();

/**
 * Controlador de fachada que recibe mensajes de la capa de presentación,
 * Instancia la base de datos,
 * Delega a otras clases las peticiones recibidas y en algunas ocasiones
 * Envía las respuestas recibidas a la capa de presentación.
 * Basado en el patron GoF "Facade", ver Larman
 * @author Carlos Cuesta Iglesias
 * Versión 2.0, 28 de agosto de 2014
 */
class Fachada {

    public function __construct() {
        try {
            date_default_timezone_set('America/Bogota');
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', 'log.txt');

            include_once('config.php');
            // iniciar la sesión, solo si no existe. Esto debe ir antes de enviar cualquier cosa al navegador
            session_start();
            $this->ejecutar($_REQUEST);
        } catch (Exception $e) {
            error_log("Problemas en $clase::$metodo()\n" . $e->getMessage());
            echo json_encode(array("ok" => FALSE, "mensaje" => $e->getMessage()));
        }
    }

    /**
     * Ejecuta una operación solicitada por la capa de presentación
     * @param array $args Los datos recibidos de la capa de presentación 
     * @throws Exception En caso de no encontrarse la clase o el método solicitado
     */
    function ejecutar($args) {
//    // Verifique el estado de las sesiones
//    if (session_status() === PHP_SESSION_ACTIVE) {
//        error_log('ID de la sesión: ' . session_id());
//    } else if (session_status() === PHP_SESSION_NONE) {
//        error_log('No hay una sesión activa');
//    } else if (session_status() === PHP_SESSION_DISABLED) {
//        error_log('Las sesiones están deshabilitadas');
//    }
        // agregar un objeto de tipo PDO a los argumentos que se enviarán al método
        $args['conexion'] = new UtilConexion();
        // obtener los nombres de la clase y el método
        $clase = $args['clase'];
        $metodo = $args['oper'];

        if (class_exists($clase)) {
//        Un simple control de log        
//        if ($metodo == 'del' || $metodo == 'edit') {
//            file_put_contents(Utilidades::$dir_reporte . 'log.txt', date("Y-m-d H:i:s") . ' ' . $_SESSION['usuario'] . ' ' . $clase . '.' . $metodo . "(...)\n", FILE_APPEND);
//        }
            if (substr($clase, 0, 4) === 'Util') {
                if (method_exists($clase, $metodo)) {
                    $clase::$metodo($args);
                } else {
                    throw new Exception("Imposible responder al mensaje enviado. Argumentos recibidos:\n" . print_r($_REQUEST, 1));
                }
            } else {
                $obj = new $clase();
                if (method_exists($obj, $metodo)) {
                    $obj->{$metodo}($args);
                } else {
                    throw new Exception("Imposible responder al mensaje enviado. Argumentos recibidos:\n" . print_r($_REQUEST, 1));
                }
            }
        } else {
            throw new Exception("No se pudo definir un receptor de mensajes. Argumentos recibidos:\n" . print_r($_REQUEST, 1));
        }
    }

}
