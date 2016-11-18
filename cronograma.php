<?php

/**
 * Description of OrdenProduccion
 * Implementa el CRUD para las órdenes de producción
 * @author Administrador
 */
class cronograma {
    
    /*function add($param) {
        extract($param);
        $sql = "INSERT INTO pruebas values('$inicio_periodo','$fin_periodo','$grupo','$sala','$dia','$hora','$horas')";
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();
    }*/
    function add($param){
        extract($param);

        $inicio = new DateTime($inicio_periodo);
        $fin = new DateTime($fin_periodo);
        $interval = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($inicio, $interval, $fin);



        foreach ($fechas as $fecha) {
            $fecha = $fecha->format('Y-m-d');
            $inicio = "$fecha";
            $fin = "$fecha";
            $diaSemana = date("l", strtotime($fecha));
            if($dia==$diaSemana){
                $sql = "INSERT INTO pruebas values('$inicio','$fin','$grupo','$sala','$dia','$inicio_hora','$fin_hora')";
                $conexion->getPDO()->exec($sql);
            }
        }
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

}
