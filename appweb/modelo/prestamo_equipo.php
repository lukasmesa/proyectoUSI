<?php

class prestamo_equipo {
    
    function add($param) {
        extract($param);
        
        $sql = "INSERT INTO prestamo_equipo (fecha_inicio,fecha_fin,id_usuario,fk_equipo) values('$fecha_inicio','$fecha_fin','$id_usuario','$fk_equipo')";

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE prestamo_equipo
                       SET fecha_inicio = '$fecha_inicio',fecha_fin='$fecha_fin',
					   id_usuario='$id_usuario', fk_equipo='$fk_equipo'
                       WHERE codigo_prestamo = '$id';";
       
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM prestamo_equipo WHERE codigo_prestamo = '$id';");
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
        $sql = "SELECT p.codigo_prestamo,p.fecha_inicio,p.fecha_fin,p.id_usuario,a.nombre,p.fk_equipo from prestamo_equipo p, usuario a where(p.id_usuario=a.id_usuario)";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['codigo_prestamo'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                       // $fila['codigo_prestamo'],
					    $fila['fecha_inicio'],
                        $fila['fecha_fin'],
                        $fila['id_usuario'],
                        $fila['fk_equipo'],
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