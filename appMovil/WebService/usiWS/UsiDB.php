<?php

/**
 * Created by PhpStorm.
 * User: cvem8165
 * Date: 12/11/16
 * Time: 06:02 PM
 */
class UsiDB
{
    protected $pgsql;

    public function __construct()
    {
        $this->pgsql = pg_connect("host=dijansoft.xyz dbname=usi user=usiuser password=usiapp")
        or die("No se ha podido conectar:  " . pg_last_error());

    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function getSalas($nombre_sala = 'vacio', $estado = 0)
    {
        ini_set('date.timezone', 'America/Bogota');
        //$fecha = date('Y-m-d');

        $hora = date(("H"));
        $hora = '13'; // la 1

        $fecha = '2016-11-30';


        if ($nombre_sala != 'vacio') {
            $nombre_sala = str_replace('_',' ', $nombre_sala);

            $sql = "SELECT s.*, c.fecha_reserva, c.fecha_ini_prestamo, c.fecha_fin_prestamo FROM
            SALA s INNER JOIN cronograma c ON s.id_sala = c.id_sala
            WHERE c.estado_reserva = $1 AND to_char(c.fecha_fin_prestamo,'YYYY-MM-DD') = $2
            AND s.nombre_sala = $3
            AND to_number(to_char(c.fecha_ini_prestamo,'HH24'),'99') 
            BETWEEN to_number($4, '99') -1  AND 23;";

            $result = pg_query_params($this->pgsql, $sql, Array($estado, $fecha , $nombre_sala, $hora));
            if ($result) {
                $json = Array();
                $indice = 0;
                while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                    $json[$indice] = $row;
                    $indice++;

                }
                return json_encode($json, JSON_PRETTY_PRINT);
                /*
                while ($row = pg_fetch_row($result)){
                    echo $row[0];
                    $json[$indice]['nombre_sala'] = $row[0];
                    $json[$indice]['capacidad'] = $row[1];
                    $json[$indice]['decripcion'] = $row[2];
                    $json[$indice]['nombre_bloque'] = $row[3];
                    $indice++;
                }
                */


            } else {
                echo "Error "; //.pg_last_error();
            }
        }
        else{
            $sql = "SELECT s.*, c.fecha_reserva,  c.fecha_ini_prestamo, c.fecha_fin_prestamo FROM
            SALA s INNER JOIN cronograma c ON s.id_sala = c.id_sala
            WHERE c.estado_reserva = $1 AND to_char(c.fecha_fin_prestamo,'YYYY-MM-DD') = $2
            AND to_number(to_char(c.fecha_ini_prestamo,'HH24'),'99') 
            BETWEEN to_number($3, '99') -1  AND 23;";

            $result = pg_query_params($this->pgsql, $sql, Array($estado,$fecha ,$hora));
            if ($result) {
                $json = Array();
                $indice = 0;
                while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                    $json[$indice] = $row;
                    $indice++;

                }
                return json_encode($json, JSON_PRETTY_PRINT);




            } else {
                echo "Error "; //.pg_last_error();
            }
        }



        return 'Nada';

    }

    public function getMonitorias()
    {
        ini_set('date.timezone', 'America/Bogota');
        //$fecha = date('Y-m-d');

        $fecha = '2016'; //esta fecha se reemplaza mas adelante por la fecha actual

        //la fecha la colocamos como parametro

        $sql = "select to_char(c.fecha_ini_prestamo, 'HH24:MI') as hora, s.nombre_sala as sala, u.id_usuario as codigo,  u.nombre, u.apellido, s.descripcion, s.capacidad, u.correo as correo
		from usuario u join monitor m on u.id_usuario = m.id_usuario
        join cronograma c on c.id_usuario = m.id_usuario
        join sala s on c.id_sala = s.id_sala
        where to_char(c.fecha_ini_prestamo,'YYYY') = $1
        order by hora;";

        $result = pg_query_params($this->pgsql, $sql, Array($fecha));
        if ($result) {
            $json = Array();
            $indice = 0;
            while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                $json[$indice] = $row;
                $indice++;

            }
            return json_encode($json, JSON_PRETTY_PRINT);



        } else {
            echo "Error "; //.pg_last_error();
        }

        return 'Nada';


    }

    public function getMonitoriasOrdenEspecial(){
        ini_set('date.timezone', 'America/Bogota');
        //$fecha = date('Y-m-d');

        $fecha = '2016'; //esta fecha se reemplaza mas adelante por la fecha actual

        //la fecha la colocamos como parametro

        $sql = "select to_char(c.fecha_ini_prestamo, 'HH24:MI') as hora, s.nombre_sala as sala, u.id_usuario as codigo,  u.nombre, u.apellido, s.descripcion, s.capacidad, u.correo as correo
		from usuario u join monitor m on u.id_usuario = m.id_usuario
        join cronograma c on c.id_usuario = m.id_usuario
        join sala s on c.id_sala = s.id_sala
        where to_char(c.fecha_ini_prestamo,'YYYY') = $1
        order by hora;";

        $result = pg_query_params($this->pgsql, $sql, Array($fecha));
        if ($result) {
            $json = Array();
            $indice = 0;
            while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                $json[$indice] = $row;
                $indice++;

            }
            
            // algoritmo eduard

            $marcado = array();
			for ( $k = 0; $k < count($json); $k++ ){
				$marcado[$k] = true;
			}

            $retorno = array();
            $contador = 0;
            $j = 0;
            $aux = $json;
            while ( count($json) > 0){
                    
                $obj =  array_shift($json); // Remueve el prime elemento de la lista
                
                $letra = $obj['hora'];
                $cadena = substr($letra,0,strpos($letra,":"));
                
                $entero = $cadena*1;
                
                if( $marcado[$j] ){
                    $tem = array('hora' => $entero.":00 - ".($entero+2).":00 ",'estudiante'=>array());
                    for($i = 0; $i < count( $aux); $i++){
                        
                        $letra = $aux[$i]['hora'];
                        $cadena = substr($letra,0,strpos($letra,":"));
                        if( $cadena*1 >= $entero && $cadena*1 <= $entero + 2) { //+2 horas 
                            array_push($tem['estudiante'], array('sala' => $aux[$i]['sala'],'codigo'=> $aux[$i]['codigo'],'nombre'=> $aux[$i]['nombre'],'nombre'=> $aux[$i]['nombre'],'apellido'=> $aux[$i]['apellido'],'correo'=> $aux[$i]['correo'],'descripcion'=> $aux[$i]['descripcion'],'capacidad'=> $aux[$i]['capacidad']));
                            $marcado[$i] = false;
                            
                        }
                    }
                    $retorno[$contador] = $tem;
                    $contador++;
                }
                $j++;
            }
            
            // fin de algoritmo eduard
            
            return json_encode($retorno, JSON_PRETTY_PRINT);




        } else {
            echo "Error "; //.pg_last_error();
        }

        return 'Nada';
    }


    public function getTodasSalasProgramadas(){
        $sql = "SELECT c.*, s.nombre_sala, s.capacidad,  s.nombre_bloque, s.color FROM
            SALA s INNER JOIN cronograma c ON s.id_sala = c.id_sala";

        $result = pg_query($this->pgsql, $sql);
        if ($result) {
            $json = Array();
            $indice = 0;
            while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                $json[$indice] = $row;
                $indice++;

            }
            return json_encode($json, JSON_PRETTY_PRINT);


        } else {
            echo "Error "; //.pg_last_error();
        }

        return 'Nada';
    }



    public function cerrarConexion(){
        pg_close($this->pgsql);
    }


}