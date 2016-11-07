<?php

class grupo {
    
    function add($param) {
        extract($param);
        $sql = "INSERT INTO asignatura values('$cod_asignatura','$nombre_asignatura')";
        $sql1 = "INSERT INTO grupo values('$codigo_grupo','$id_docente' , '$cod_asignatura' )";


        $conexion->getPDO()->exec($sql);
        $conexion->getPDO()->exec($sql1);

        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "UPDATE grupo
                       SET codigo_grupo = '$codigo_grupo', id_docente = '$id_docente' 
                       WHERE codigo_grupo = '$codigo_grupo';";
        $sql1 = "UPDATE asignatura
                       SET cod_asignatura = '$cod_asignatura', nombre_asignatura = '$nombre_asignatura'
                       WHERE cod_asignatura = '$cod_asignatura';";
        $conexion->getPDO()->exec($sql);
        $conexion->getPDO()->exec($sql1);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM grupo WHERE codigo_grupo = '$id';");
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
        $sql = "select g.codigo_grupo, g.id_docente, a.cod_asignatura , a.nombre_asignatura from grupo g inner join asignatura a on a.cod_asignatura = g.cod_asignatura $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['codigo_grupo'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['codigo_grupo'],
                        $fila['id_docente'],
                        $fila['cod_asignatura'],
                        $fila['nombre_asignatura'],
                        
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}


