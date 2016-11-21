<?php

class equipos_sala {

      function add($param) {
        extract($param);

        $sql = "insert into equipos_sala values('$id_equipo_sala','$descripcion','$estado','$software_equipo','$partes_equipo','$nombre_sala')";    

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
       
        $sql = "UPDATE equipos_sala 
                    set id_equipo_sala='$id_equipo_sala',descripcion='$descripcion',estado='$estado',
                    software_equipo='$software_equipo',partes_equipo='$partes_equipo',nombre_sala='$nombre_sala'
                    where id_equipo_sala='$id_equipo_sala';";
            
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

 
    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("delete from equipos_sala where id_equipo_sala = '$id';");
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
        $sql = "SELECT  id_equipo_sala,descripcion,estado,software_equipo,partes_equipo,nombre_sala from equipos_sala $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página
        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;

            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado

                $respuesta['rows'][] = [
                    'id' => $fila['id_equipo_sala'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['id_equipo_sala'],
                        $fila['descripcion'],
                        $fila['estado'],
                        $fila['software_equipo'],
                        $fila['partes_equipo'],
                        $fila['nombre_sala']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}
?>