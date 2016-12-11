<?php

class horas_disponibles_monitor {
    
    function add($param) {
        extract($param);
        
        $sql = "INSERT INTO horas_disp_monitor (dia,hora_inicio,hora_fin,id_monitor) values('$dia','$hora_inicio','$hora_fin','$id_monitor')";

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE horas_disp_monitor
                       SET  dia = '$dia',hora_inicio='$hora_inicio',
					   hora_fin='$hora_fin', id_monitor='$id_monitor'
                       WHERE id_horario = '$id';";
       
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM horas_disp_monitor WHERE id_horario = '$id';");
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
        $sql = "SELECT h.id_horario,h.dia,h.hora_inicio,h.hora_fin,h.id_monitor,a.nombre
                from horas_disp_monitor h, usuario a where(h.id_monitor=a.id_usuario)";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                $diasS = UtilConexion::$diasSemana[$fila['dia']];

                $respuesta['rows'][] = [
                    'id' => $fila['id_horario'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                     //   $fila['id_horario'],
					    $diasS,
                        $fila['hora_inicio'],
                        $fila['hora_fin'],
                        $fila['id_monitor'],
						$fila['nombre']
                     
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }
}
?>