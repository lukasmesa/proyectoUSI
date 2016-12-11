<?php

class equipos_sala {

      function add($param) {
        extract($param);

        $sql = "insert into equipos_sala values('$id_equipo_sala','$descripcion','$estado','$software_equipo','$partes_equipo','$id_sala')";    

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
       
        $sql = "UPDATE equipos_sala 
                    set id_equipo_sala='$id_equipo_sala',descripcion='$descripcion',estado='$estado',
                    software_equipo='$software_equipo',partes_equipo='$partes_equipo',id_sala='$id_sala'
                    where id_equipo_sala='$id';";
            
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
        /*$sql = "SELECT  s.id_equipo_sala,s.descripcion,s.estado,s.software_equipo,s.partes_equipo,s.id_sala,p.nombre_sala
             from equipos_sala s , sala p where (s.id_sala=p.id_sala) $where";*/
        $sql = "SELECT  s.id_equipo_sala,s.descripcion,s.estado,s.software_equipo,s.partes_equipo,s.id_sala,p.nombre_sala
             from equipos_sala s , sala p where (s.id_sala=p.id_sala) $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página
        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;

            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
              //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                $estadoEq = UtilConexion::$estadoEquipos[$fila['estado']];

                $respuesta['rows'][] = [
                    'id' => $fila['id_equipo_sala'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['id_equipo_sala'],
                        $fila['descripcion'],
                        $estadoEq,
                        $fila['software_equipo'],
                        $fila['partes_equipo'],
                        $fila['id_sala'],
                        $fila['nombre_sala']
                        //$fila['nombre_sala']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

        //funcion requerida para desplega los IDs de monitores disponibles a la hora de ingresar en una tabla que referencie este campo
    public function selectIdEquipos($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un equipo-sala</option>";
        foreach ($conexion->getPDO()->query("SELECT id_equipo_sala FROM equipos_sala") as $fila) {
            $name = $fila['id_equipo_sala'];
            $select .= "<option value='{$fila['id_equipo_sala']}'>{$name}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }

}
?>