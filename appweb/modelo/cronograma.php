<?php

/**
 * Description of OrdenProduccion
 * Implementa el CRUD para las órdenes de producción
 * @author Administrador
 */
class cronograma
{
    function agregarActividad($param){
        extract($param);
        $sql = "INSERT INTO cronograma(fecha_reserva, estado_reserva, tercero,fecha_ini_prestamo,fecha_fin_prestamo,tipo,descripcion,id_usuario,nombre_sala) VALUES ('{$turno['fecha_reserva']}', '{$turno['estado_reserva']}', '{$turno['tercero']}','{$turno['fecha_ini_prestamo']}', '{$turno['fecha_fin_prestamo']}', '{$turno['tipo']}', '{$turno['descripcion']}', '{$turno['id_usuario']}', '{$turno['nombre_sala']}')";
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }
    public function getProgramacion($param) {
        $timezone = null;
        extract($param);
        if (!isset($start) || !isset($end)) {
            error_log("No se ha proporcionado un rango de datos");
        }
        // Analizar los parámetros start / end
        // Estos se supone que son cadenas ISO8601 sin tiempo ni zona horaria, como: "2013-12-29".
        // Puesto que ninguna zona horaria estará presente, serán interpretados como UTC (Universal Time Coordinate)
        $rangoInicio = Utilidades::stringComoDateTime($start)->format('Y-m-d H:i:s');
        $rangoFin = Utilidades::stringComoDateTime($end)->format('Y-m-d H:i:s');
        // Analizar si está presente el parámetro timezone
        if (isset($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }
        /*$sql = "SELECT id_turno_produccion, hora_inicio, hora_fin, fk_maquina, maquina.color, maquina.descripcion
                  FROM turno_produccion JOIN maquina ON maquina.id_maquina = turno_produccion.fk_maquina WHERE $condicion";*/
        $sql = "SELECT id_reserva, fecha_reserva, estado_reserva, tercero, fecha_ini_prestamo, fecha_fin_prestamo, tipo, descripcion, id_usuario, nombre_sala FROM cronograma ";
        error_log($sql);
        // error_log($sql);
        if (($rs = $conexion->getPDO()->query($sql))) {
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $fila['id'] = $fila['id_reserva'];
                // Luego de las siguientes asignaciones se podrían borrar (unset) hora_inicio y hora_fin pero no vale la pena
                $fila['start'] = $fila['fecha_ini_prestamo'];
                $fila['end'] = $fila['fecha_fin_prestamo'];
                $fila['title'] = $fila['descripcion'] . ' En la ' . $fila['nombre_sala'];
                //$fila['nombres']= $fila['id_usuario'];
                $evento = new Evento($fila, $timezone);
//              if ($evento->estaDentroDelRango($rangoInicio, $rangoFin)) {  $turnos[] = $evento->comoArray(); } // otra forma de filtrar eventos
                $turnos[] = $evento->comoArray();
            }
        }
        $conexion->getEstado();
        error_log(print_r($turnos, 1));  // <-- por si se quiere ver cómo queda el array asociativo de turnos
        echo json_encode($turnos);
    }
    public function actualizarActividad($param) {
        extract($param);
        if ($caso == 'mover') {
            $sql = "UPDATE turno_produccion SET hora_inicio='{$turno['start']}', hora_fin='{$turno['end']}' WHERE id_turno_maquina='{$turno['id']}'";
        } else if ($caso == 'actualizar') {
            $sql = "UPDATE cronograma SET fecha_ini_prestamo='{$turno['start']}', fecha_fin_prestamo='{$turno['end']}', tipo='{$turno['tipo']}',descripcion='{$turno['descripcion']}',id_usuario='{$turno['id_usuario']}',nombre_sala='{$turno['sala']}' WHERE id_reserva='{$turno['id_reserva']}'";
        } else if ($caso == 'redimensionar') {
            $sql = "UPDATE cronograma SET hora_fin='{$turno['end']}' WHERE id_reserva='{$turno['id_reserva']}'";
        }
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }
    public function eliminarActividad($param) {
        extract($param);
        $sql = "DELETE FROM cronograma WHERE id_reserva=$idCronograma";
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }
    function omitirCruces($param){
        extract($param);

        $inicio = new DateTime($inicio_periodo);
        $hora_inicio = $inicio->format('H:i');
        $fin = new DateTime($fin_periodo);
        $hora_fin = $fin->format('H:i');
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);
        $today = date("Y-m-d H:i");
        $estado = "Disponible";
        $mensaje = '';
        $selectSQL = "SELECT id_reserva, fecha_ini_prestamo,fecha_fin_prestamo, tipo, descripcion, id_usuario, nombre_sala FROM cronograma 
                      WHERE '$sala'= nombre_sala";
        if (($rs = $conexion->getPDO()->query($selectSQL))) {
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                foreach ($fechas as $fecha) {
                    $fecha = $fecha->format('Y-m-d');
                    $inicio = "$fecha $hora_inicio";
                    $fin = "$fecha $hora_fin";
                    $diaSemana = date("l", strtotime($fecha));
                    if ($dia == $diaSemana) {
                        if ($this->check_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $inicio)
                            || $this->check_out_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $fin)
                            || $this->check_range($inicio, $fin, $fila['fecha_ini_prestamo'])
                            || $this->check_out_range($inicio, $fin, $fila['fecha_fin_prestamo'])) {
                            $mensaje .= "$inicio -- $fin -- $sala\n";
                        }
                        else{
                            $sql = "INSERT INTO cronograma (fecha_reserva, estado_reserva,fecha_ini_prestamo,fecha_fin_prestamo,tipo,descripcion,id_usuario,nombre_sala)
                                     values('$today','$estado','$inicio','$fin','$tipo','$descripcion','$usuario','$sala')";
                            $conexion->getPDO()->exec($sql);
                        }
                    }
                }
            }
            if($mensaje){
                $mensaje = "fallo en la insercion de los siguientes registros:\n$mensaje";
                error_log($mensaje);
            }
        }
        //error_log(print_r($respuesta, TRUE));
        echo $conexion->getEstado();
    }

    function reemplazarCruces(){
        xtract($param);

        $inicio = new DateTime($inicio_periodo);
        $hora_inicio = $inicio->format('H:i');
        $fin = new DateTime($fin_periodo);
        $hora_fin = $fin->format('H:i');
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);
        $today = date("Y-m-d H:i");
        $estado = "Disponible";
        $mensaje = '';
        $selectSQL = "SELECT id_reserva, fecha_reserva, fecha_ini_prestamo,fecha_fin_prestamo, tipo, descripcion, id_usuario, nombre_sala FROM cronograma 
                      WHERE '$sala'= nombre_sala";
        if (($rs = $conexion->getPDO()->query($selectSQL))) {
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                foreach ($fechas as $fecha) {
                    $fecha = $fecha->format('Y-m-d');
                    $inicio = "$fecha $hora_inicio";
                    $fin = "$fecha $hora_fin";
                    $today = date("Y-m-d H:i");
                    $new_id = $fila['id_reserva'];
                    $diaSemana = date("l", strtotime($fecha));
                    if ($dia == $diaSemana) {
                        if ($this->check_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $inicio)
                            || $this->check_out_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $fin)
                            || $this->check_range($inicio, $fin, $fila['fecha_ini_prestamo'])
                            || $this->check_out_range($inicio, $fin, $fila['fecha_fin_prestamo'])) {
                            $mensaje .= "$inicio -- $fin -- $sala\n";
                            $sql = "UPDATE cronograma
                                   SET fecha_reserva = '$today', fecha_ini_prestamo = '$inicio_periodo', fecha_fin_prestamo = '$fin_periodo', tipo = '$tipo',
                                    descripcion = '$descripcion', id_usuario = '$usuario', nombre_sala ='$sala'
                                     WHERE $id_reserva = '$new_id';";
                            $conexion->getPDO()->exec($sql);
                        }
                        else{
                            $sql = "INSERT INTO cronograma (fecha_reserva, estado_reserva,fecha_ini_prestamo,fecha_fin_prestamo,tipo,descripcion,id_usuario,nombre_sala)
                                     values('$today','$estado','$inicio','$fin','$tipo','$descripcion','$usuario','$sala')";
                            $conexion->getPDO()->exec($sql);
                        }
                    }
                }
            }
            if($mensaje){
                $mensaje = "fallo en la insercion de los siguientes registros:\n$mensaje";
                error_log($mensaje);
            }
        }
        //error_log(print_r($respuesta, TRUE));
        echo $conexion->getEstado();
    }


    function add($param)
    {
        extract($param);

        $inicio = new DateTime($inicio_periodo);
        $hora_inicio = $inicio->format('H:i');
        $fin = new DateTime($fin_periodo);
        $hora_fin = $fin->format('H:i');
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);
        $today = date("Y-m-d H:i");
        $estado = "Disponible";
        $mensaje = '';
        $selectSQL = "SELECT id_reserva, fecha_ini_prestamo,fecha_fin_prestamo, tipo, descripcion, id_usuario, nombre_sala FROM cronograma 
                      WHERE '$sala'= nombre_sala";
        if (($rs = $conexion->getPDO()->query($selectSQL))) {
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                foreach ($fechas as $fecha) {
                    $fecha = $fecha->format('Y-m-d');
                    $inicio = "$fecha $hora_inicio";
                    $fin = "$fecha $hora_fin";
                    $diaSemana = date("l", strtotime($fecha));
                    if ($dia == $diaSemana) {
                        if ($this->check_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $inicio)
                            || $this->check_out_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $fin)
                            || $this->check_range($inicio, $fin, $fila['fecha_ini_prestamo'])
                            || $this->check_out_range($inicio, $fin, $fila['fecha_fin_prestamo'])) {
                            $mensaje .= "$inicio -- $fin -- $sala\n";
                        }
                    }
                }
            }
            if($mensaje){
                $mensaje = "fallo en la insercion de los siguientes registros:\n$mensaje";
                error_log($mensaje);
            }
            else{
                foreach ($fechas as $fecha) {
                    $fecha = $fecha->format('Y-m-d');
                    $inicio = "$fecha $hora_inicio";
                    $fin = "$fecha $hora_fin";
                    $diaSemana = date("l", strtotime($fecha));
                    if($dia==$diaSemana){
                        $sql = "INSERT INTO cronograma (fecha_reserva, estado_reserva,fecha_ini_prestamo,fecha_fin_prestamo,tipo,descripcion,id_usuario,nombre_sala)
                  values('$today','$estado','$inicio','$fin','$tipo','$descripcion','$usuario','$sala')";
                        $conexion->getPDO()->exec($sql);
                    }
                }
            }
            
        }
        //error_log(print_r($respuesta, TRUE));
        echo $conexion->getEstado();
    }

    function edit($param)
    {
        extract($param);

        $sql = "UPDATE cronograma
                       SET fecha_ini_prestamo = '$inicio_periodo', fecha_fin_prestamo = '$fin_periodo', tipo = '$tipo', descripcion = '$descripcion',
                       id_usuario = '$usuario', nombre_sala ='$sala' WHERE $id_reserva = '$id';";
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param)
    {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM cronograma WHERE id_reserva = '$id';");
        echo $conexion->getEstado();

    }

    /**
     * Procesa las filas que son enviadas a un objeto jqGrid
     * @param type $param un array asociativo con los datos que se reciben de la capa de presentación
     */
    function select($param)
    {
        extract($param);
        $dias = array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT id_reserva, fecha_ini_prestamo,fecha_fin_prestamo, tipo, descripcion, id_usuario, nombre_sala FROM cronograma $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;

            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado

                $respuesta['rows'][] = [
                    'id' => $fila['id_reserva'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['fecha_ini_prestamo'],
                        $fila['fecha_fin_prestamo'],
                        $fila['tipo'],
                        $fila['descripcion'],
                        $fila['id_usuario'],
                        $fila['nombre_sala'],
                        $dias[date('N', strtotime($fila['fecha_ini_prestamo']))]
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

    function check_range($start_date, $end_date, $date){

        // conver to timestamp
        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        $date = strtotime($date);

        // check the date
        if(($date >= $start_date) && ($date < $end_date))
            return TRUE;
        else
            return FALSE;
    }
    function check_out_range($start_date, $end_date, $date){

        // conver to timestamp
        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        $date = strtotime($date);

        // check the date
        if(($date > $start_date) && ($date <= $end_date))
            return TRUE;
        else
            return FALSE;
    }

}
