<?php

class docente {
    
    function add($param) {
        extract($param);
        
        $sql = "do $$
                    begin
                        INSERT INTO usuario values('$id_usuario','$nombre','$apellido','$correo','$contrasena','$tipo_doc');
                        INSERT INTO docente values('$id_usuario','$color');                        
                    end$$
                ";
        
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
 
        $sql = "do $$
                    begin
                    UPDATE usuario
                       SET id_usuario = '$id_usuario', nombre = '$nombre', apellido = '$apellido', correo = '$correo', tipo_doc = '$tipo_doc'
                       WHERE id_usuario = '$id';   
                    
                       UPDATE docente
                       set color='$color'
                       WHERE id_usuario = '$id_usuario';

                       
                    end$$
                    ";
        
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $sql = "do $$
                    begin
                        DELETE FROM docente WHERE id_usuario = '$id';
                        DELETE FROM usuario WHERE id_usuario = '$id';
                    end$$
                ";

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
        $sql = "SELECT  e.id_usuario, u.tipo_doc, u.nombre, u.apellido,u.correo FROM docente e inner join usuario u on e.id_usuario = u.id_usuario";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                $tipoDoc = UtilConexion::$tipo_doc[$fila['tipo_doc']];
                $respuesta['rows'][] = [
                    'id' => $fila['id_usuario'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid                        
                        $fila['id_usuario'],
                        $tipoDoc,
                        $fila['nombre'],
                        $fila['apellido'],
                        $fila['correo']                        
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }


    //funcion requerida para desplega los IDs de docenter disponibles a la hora de ingresar en una tabla que referencie este campo
    function selectIdsDocente($param)
    {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "select d.id_usuario from docente d inner join usuario u on u.id_usuario = d.id_usuario
                union
                select a.id_usuario from administrativo a inner join usuario u2 on a.id_usuario = u2.id_usuario; ";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $respuesta['rows'][] = [
                    'id' => $fila['id_usuario'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        
                        $fila['id_usuario'],
                        
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }
    public function getSelectDocente($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un Docente</option>";
        foreach ($conexion->getPDO()->query("SELECT nombre,d.id_usuario,apellido FROM usuario u,docente d WHERE u.id_usuario=d.id_usuario") as $fila) {
            $name = $fila['id_usuario'].'-'.$fila['nombre'].' - '.$fila['apellido'];
            $select .= "<option value='{$fila['id_usuario']}'>{$name}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }

}
?>
