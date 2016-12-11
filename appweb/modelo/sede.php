<?php

class sede {

	function add($param) {
		extract($param);

		$sql = "INSERT INTO sede values('$nombre_sede','$direccion')";

		$conexion->getPDO()->exec($sql);
		echo $conexion->getEstado();
	}

	function edit($param) {
		extract($param);
		$sql = "UPDATE sede
					   SET nombre_sede = '$nombre_sede', direccion = '$direccion'
					   WHERE nombre_sede = '$id';";
		$conexion->getPDO()->exec($sql);
		echo $conexion->getEstado();
	}

 
	function del($param) {
		extract($param);
		error_log(print_r($param, TRUE));
		$conexion->getPDO()->exec("DELETE FROM sede WHERE nombre_sede = '$id';");
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
		$sql = "SELECT nombre_sede,direccion from sede $where";
		// crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
		$respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página
		// agregar al objeto que se envía las filas de la página requerida
		if (($rs = $conexion->getPDO()->query($sql))) {
			$cantidad = 999; // se pueden enviar al grid valores calculados o constantes
			$tiros_x_unidad = 2;

			while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
				//$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado

				$respuesta['rows'][] = [
					'id' => $fila['nombre_sede'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
					'cell' => [ // los campos que se muestra en las columnas del grid
						$fila['nombre_sede'],
						$fila['direccion']
					]
				];
			}
		}
		$conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
		echo json_encode($respuesta);
	}
	
	//funcion requerida para desplega los nombres de las sedes disponibles a la hora de ingresar en una tabla que referencie este campo
    function selectNombreSedes($param)
    {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "SELECT nombre_sede FROM sede";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                //$tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['nombre_sede'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        
                        $fila['nombre_sede'],
                        
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }
}
?>