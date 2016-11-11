<?php

/**
 * Description of OrdenProduccion
 * Implementa el CRUD para las órdenes de producción
 * @author Administrador
 */


class Software {

    function add($param) {
        // error_log(print_r($param, TRUE));  // cómo ver el contenido de una estructura de datos
        extract($param);
        // nunca suponga el orden de las columnas: INSERT INTO tabla VALUES (v1, v2,v3, ...); tómese el trabajo de indicar los nombres de columnas
        // los nombres de los elementos del array asociativo corresponden a los atributos name de las columnas del jqGrid
        $sql = "INSERT INTO software(id_software, nombre, descripcion) VALUES ('$id_software', '$nombre','$descripcion');";
        //error_log($sql); // cómo ver el contenido de una variable suscrita (dato atómico)
        $conexion->getPDO()->exec($sql);
		
		//pg_query($this->conexion,$sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
        $sql = "UPDATE software SET id_software='$id_software', nombre='$nombre', descripcion='$descripcion'
                WHERE id_software = '$id_software';";  // <-- el ID de la fila asignado en el SELECT permite construir la condición de búsqueda del registro a modificar
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM software WHERE id_software = '$id_software';");
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
        $sql = "select id_software,nombre,descripcion from software where id_software = '$id_software';";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página
        //error_log($sql);  // descomente para generar un log y probar si la instruccion tiene errores
        // puede examinar aquí con error_log(..) el contenido de las variables 

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['id_software'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['id_software'],
                        $fila['nombre'],
                        $fila['descripcion'],
                        $cantidad,  // <-- envío al grid de un valor calculado
                        $tiros_x_unidad,
                        $tipoEstado  // <-- OJO
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}

?>
