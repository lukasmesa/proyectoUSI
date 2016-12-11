<?php

/**
 * Description of cronograma
 * Implementa el CRUD para cronograma

 */
class cronograma {
    
    
    function add($param)
    {
        extract($param);
        $dias = array('', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $inicio = new DateTime($inicio_periodo);
        $hora_inicio = $inicio->format('H:i');
        $fin = new DateTime($fin_periodo);
        $hora_fin = $fin->format('H:i');
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);
        $today = date("Y-m-d H:i");
        $estado = 0;
        $mensaje = '';
        $selectSQL = "SELECT id_reserva, fecha_ini_prestamo,fecha_fin_prestamo, tipo, descripcion, id_usuario, id_sala FROM cronograma 
                      WHERE '$sala'= id_sala";
        if (($rs = $conexion->getPDO()->query($selectSQL))) {
            foreach ($fechas as $fecha) {
                $fecha = $fecha->format('Y-m-d');
                $inicio = "$fecha $hora_inicio";
                $fin = "$fecha $hora_fin";
                $diaSemana = date("w", strtotime($fecha));
                if ($dia == $diaSemana) {
                    if($this->checkCollision($inicio, $fin, $rs)){
                        $mensaje .= "$inicio - $fin // \n";
                    }else{
                        $sql = "INSERT INTO cronograma (fecha_reserva,fecha_ini_prestamo,fecha_fin_prestamo,descripcion,id_usuario,id_sala, tipo, estado_reserva)
                  values('$today','$inicio','$fin','$descripcion','$usuario','$sala','$tipo', '$estado')";
                        $conexion->getPDO()->exec($sql);
                    }
                }
            }
            if($mensaje){
                $mensaje = "Fallo en la inserción de los siguientes registros:\n$mensaje";
                echo $mensaje;
                error_log($mensaje);
            }
        }
    }
    function checkCollision($inicio, $fin, $rs){
        while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
            if ($this->check_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $inicio)
                || $this->check_out_range($fila['fecha_ini_prestamo'], $fila['fecha_fin_prestamo'], $fin)
                || $this->check_range($inicio, $fin, $fila['fecha_ini_prestamo'])
                || $this->check_out_range($inicio, $fin, $fila['fecha_fin_prestamo'])) {
                return TRUE;
            }
        }
        return FALSE;
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
        $tipoActividad = array('', 'Monitoría', 'Clase', 'Evento');
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT cronograma.id_reserva, cronograma.fecha_ini_prestamo, cronograma.fecha_fin_prestamo, cronograma.tipo, cronograma.descripcion, 
cronograma.id_usuario, cronograma.id_sala, sala.nombre_sala, usuario.nombre, usuario.apellido from 
cronograma, sala, usuario where cronograma.id_sala=sala.id_sala and cronograma.id_usuario=usuario.id_usuario; $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;

            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado

                $respuesta['rows'][] = [
                    'id' => $fila['id_reserva'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['fecha_ini_prestamo'],
                        $fila['fecha_fin_prestamo'],
                        $tipoActividad[$fila['tipo']],
                        $fila['descripcion'],
                        $fila['nombre'].' '.$fila['apellido'],
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
        if($caso=='Docente') {
            $sql="SELECT cronograma.id_reserva, cronograma.fecha_reserva,cronograma.fecha_ini_prestamo, cronograma.fecha_fin_prestamo,cronograma.descripcion, cronograma.id_usuario, cronograma.id_sala, docente.color FROM cronograma,docente,usuario WHERE cronograma.id_usuario=docente.id_usuario and usuario.id_usuario=cronograma.id_usuario";
        }else if($caso=='Sala'){
            $sql="SELECT cronograma.id_reserva, cronograma.fecha_reserva,cronograma.fecha_ini_prestamo, cronograma.fecha_fin_prestamo,cronograma.descripcion, cronograma.id_usuario,sala.color,usuario.nombre FROM cronograma,sala,usuario WHERE cronograma.id_sala=sala.id_sala and usuario.id_usuario=cronograma.id_usuario";

        }else if($caso=='Monitor'){
            $sql="SELECT cronograma.id_reserva, cronograma.fecha_reserva,cronograma.fecha_ini_prestamo, cronograma.fecha_fin_prestamo,cronograma.descripcion, cronograma.id_usuario, cronograma.id_sala, monitor.color FROM cronograma,monitor,usuario WHERE cronograma.id_usuario=monitor.id_usuario and usuario.id_usuario=cronograma.id_usuario";

        }

        error_log($sql);
        // error_log($sql);

        if (($rs = $conexion->getPDO()->query($sql))) {
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $fila['id'] = $fila['id_reserva'];
                // Luego de las siguientes asignaciones se podrían borrar (unset) hora_inicio y hora_fin pero no vale la pena
                $fila['start'] = $fila['fecha_ini_prestamo'];
                $fila['end'] = $fila['fecha_fin_prestamo'];
                $fila['title'] = $fila['descripcion'];
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
            $sql = "UPDATE cronograma SET fecha_ini_prestamo='{$turno['start']}', fecha_fin_prestamo='{$turno['end']}' WHERE id_reserva='{$turno['id_reserva']}'";
        } else if ($caso == 'actualizar') {
            $sql = "UPDATE cronograma SET fecha_ini_prestamo='{$turno['start']}', fecha_fin_prestamo='{$turno['end']}', tipo='{$turno['tipo']}',descripcion='{$turno['descripcion']}',id_usuario='{$turno['id_usuario']}',id_sala='{$turno['sala']}' WHERE id_reserva='{$turno['id_reserva']}'";
        } else if ($caso == 'redimensionar') {
            $sql = "UPDATE cronograma SET fecha_fin_prestamo='{$turno['end']}' WHERE id_reserva='{$turno['id_reserva']}'";
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
    function agregarActividad($param){
        extract($param);
        $sql = "INSERT INTO cronograma(fecha_reserva, estado_reserva,fecha_ini_prestamo,fecha_fin_prestamo,tipo,descripcion,id_usuario,id_sala) VALUES ('{$turno['fecha_reserva']}', '{$turno['estado_reserva']}', '{$turno['fecha_ini_prestamo']}', '{$turno['fecha_fin_prestamo']}', '{$turno['tipo']}', '{$turno['descripcion']}', '{$turno['id_usuario']}', '{$turno['id_sala']}')";
        $conexion->getPDO()->exec($sql);

        echo $conexion->getEstado();

    }

}
?>
