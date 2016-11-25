<?php

/**
 * Description of cronograma
 * Implementa el CRUD para cronograma

 */
class cronograma {
    
    function add($param) {
        extract($param);
        
        $sql = "INSERT INTO pruebas values('$inicio_periodo','$fin_periodo','$grupo','$sala','$dia','$hora','$horas')";

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }
  

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE pruebas
                       SET inicio_periodo = '$inicio_periodo', fin_periodo = '$fin_periodo', grupo = '$grupo', sala = '$sala',
                       dia = '$dia', hora = '$hora',horas = '$horas'
                       WHERE inicio_periodo = '$id';";
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM pruebas WHERE inicio_periodo = '$id';");
        echo $conexion->getEstado();
    }

    /**
     * Procesa las filas que son enviadas a un objeto jqGrid
     * @param type $param un array asociativo con los datos que se reciben de la capa de presentación
     */


    function select($param) {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT inicio_periodo,fin_periodo, grupo, sala, dia, hora, horas FROM pruebas $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;

            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['inicio_periodo'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['inicio_periodo'],
                        $fila['fin_periodo'],
                        $fila['grupo'],
                        $fila['sala'],
                        $fila['dia'],
                        $fila['hora'],
                        $fila['horas']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }


      function agregarActividad($param){
        extract($param);
        $sql = "INSERT INTO cronograma(fecha_reserva, estado_reserva,fecha_ini_prestamo,fecha_fin_prestamo,tipo,descripcion,id_usuario,nombre_sala) VALUES ('{$turno['fecha_reserva']}', '{$turno['estado_reserva']}','{$turno['fecha_ini_prestamo']}', '{$turno['fecha_fin_prestamo']}', '{$turno['tipo']}', '{$turno['descripcion']}', '{$turno['id_usuario']}', '{$turno['nombre_sala']}')";
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
        $sql = "SELECT id_reserva, fecha_reserva, estado_reserva, fecha_ini_prestamo, fecha_fin_prestamo, tipo, descripcion, id_usuario, nombre_sala FROM cronograma ";
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

}
?>