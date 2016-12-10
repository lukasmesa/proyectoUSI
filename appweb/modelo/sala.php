<?php

class sala {
    
    function add($param) {
        extract($param);
        
        $sql = "INSERT INTO sala(nombre_sala,capacidad,descripcion,nombre_bloque,color) values('$nombre_sala','$capacidad','$descripcion','$nombre_bloque','$color')";

        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE sala
                       SET nombre_sala = '$nombre_sala', capacidad = '$capacidad',descripcion='$descripcion',
                       nombre_bloque='$nombre_bloque',color='$color'				   
                       WHERE id_sala = '$id';";
       
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM sala WHERE id_sala = '$id';");
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
        $sql = "SELECT s.id_sala,s.nombre_sala, s.capacidad, s.descripcion, s.nombre_bloque "
                . " FROM sala s inner join bloque b on s.nombre_bloque = b.nombre_bloque   $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['id_sala'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                       
                       $fila['nombre_sala'],
                       $fila['capacidad'],
                       $fila['descripcion'],
                       $fila['nombre_bloque']
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

    //funcion requerida para desplegar los nombres de salas disponibles a la hora de ingresar en una tabla que referencie este campo
    function selectIdsSala($param)
    {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT id_sala FROM sala";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['id_sala'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        
                        $fila['id_sala'],
                        
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }
	
	public function getSelectSala($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione una sala</option>";
        foreach ($conexion->getPDO()->query("SELECT nombre_sala, id_sala, nombre_bloque FROM sala ORDER BY (nombre_bloque, nombre_sala)") as $fila) {
            $name = 'Bloque: '.$fila['nombre_bloque'].' - '.$fila['nombre_sala'];
            $select .= "<option value='{$fila['id_sala']}'>{$name}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }
}
?>