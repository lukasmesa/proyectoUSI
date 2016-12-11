<?php

/**
 * Created by PhpStorm.
 * User: cvem8165
 * Date: 12/11/16
 * Time: 05:48 PM
 */


require_once './UsiDB.php';

class  UsiAPI
{
    public function __construct()
    {
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }


    public function API()
    {
        header('Content-Type: application/JSON');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'GET') {
            ;
            $this->get($_GET['accion']);
        }

    }


    public function get($action)
    {

        switch ($action) {
            case 'salas':
                $db = new UsiDB();
                if (isset($_GET['estado'])) {//muestra 1 solo registro si es que existiera ID
                    if (isset($_GET['nombre'])) {
                        $db = new UsiDB();

                        // el nombre de las salas se pasa asi: sala_h
                        $response = $db->getSalas($_GET['nombre'], $_GET['estado']);
                        echo $response;
                        $db->cerrarConexion();
                    } else {
                        $response = $db->getSalas('vacio', $_GET['estado']);
                        echo $response;
                        $db->cerrarConexion();
                    }


                } else { //muestra todos los registros
                    echo 'Parametros no validos';
                }

                break;
            case 'todasprogramadas':

                $db = new UsiDB();
                $response = $db->getTodasSalasProgramadas();
                echo $response;
                $db->cerrarConexion();
                break;

            case 'monitorias':
                if(isset($_GET['orden'])){
                    if($_GET['orden'] == 'True'){
                        $db = new UsiDB();
                        $response = $db->getMonitoriasOrdenEspecial();
                        echo $response;
                        $db->cerrarConexion();

                    }
                    else { //muestra todos los registros
                        echo 'Parametros no validos';
                    }

                }
                else{
                    $db = new UsiDB();
                    $response = $db->getMonitorias();
                    echo $response;
                    $db->cerrarConexion();
                }

                break;


        }

    }
}
