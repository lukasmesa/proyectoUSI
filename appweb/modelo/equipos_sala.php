<?php

class equipos_sala {

      function add($param) {
        extract($param);

        $sql = "DO $$
            BEGIN
               	INSERT INTO equipos_sala values('$id_equipo_sala','$descripcion','$estado','$nombre_sala');
                INSERT INTO parte_equipo values('$id_equipo_sala','$id_parte');
                INSERT INTO software_equipos values('$id_equipo_sala','$id_sofware');    
            END$$;";

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
       $sql = "DO $$
       BEGIN  
            UPDATE equipos_sala
               SET id_equipo_sala = '$id_equipo_sala', descripcion = '$descripcion', estado='$estado', nombre_sala='$nombre_sala'
               WHERE id_equipo_sala = '$id_equipo_sala';
            UPDATE parte_equipo           
                SET id_equipo_sala = '$id_equipo_sala', id_parte='$id_parte'
                where id_equipo_sala='$id_equipo_sala' and id_parte='$id_parte';
            UPDATE software_equipos
                set id_equipo_sala='$id_equipo_sala',id_sofware='$id_sofware'
                where id_equipo_sala='$id_equipo_sala' and id_sofware='$id_sofware';
        END$$";
       
               
            
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

 
    function del($param) {
        extract($param);
        $sql = "do $$
                    BEGIN
                        delete from equipos_sala where id_equipo_sala='$id';
                        delete from parte_equipo where id_equipo_sala='$id';
                        delete from software_equipos where id_equipo_sala='$id';
                    end$$
                ";
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec($sql);
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
        $sql = "SELECT  E.id_equipo_sala, E.descripcion, E.estado, E.nombre_sala, s.id_parte, se.id_sofware 
                FROM ( equipos_sala E left join parte_equipo s on  E.id_equipo_sala= s.id_equipo_sala)
                left join software_equipos se on E.id_equipo_sala = se.id_equipo_sala $where";
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
                        $fila['nombre_sala'],
                        $fila['id_parte'],
                        $fila['id_sofware']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}
