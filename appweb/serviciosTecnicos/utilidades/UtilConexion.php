<?php

/**
 * Description of Conexion:
 * Ise encarga de proporcionar la conexión a la base de datos.
 * @author Carlos Cuesta Iglesias
 */
class UtilConexion {

    private $pdo;
    public static $tipoEstadoProduccion;

    public function __construct() {
        // estas constantes se utilizan tanto en vista como en modelo, por lo tanto se definen una vez y se utilizan N veces (nada de duplicar código)
        self::$tipoEstadoProduccion = ['0' => 'Seleccione un estado', '1' => 'Pendiente', '2' => 'En producción', '3' => 'Terminado'];

        try {
            $this->pdo = new PDO("pgsql:host=" . SERVIDOR . " port=" . PUERTO . " dbname=" . BASE_DATOS, USUARIO, CONTRASENA);
        } catch (PDOException $e) {
            error_log('estado utilConexion::__construct');
            error_log(utf8_encode($e));
            throw new Exception('No se pudo establecer la conexión con la base de datos', $e->getCode());
        }
    }

    public function getPDO() {
        return $this->pdo;
    }

    /**
     * Este método crea una cláusula WHERE que se utiliza para seleccionar elementos
     * de acuerdo un criterio de búsqueda dado en el formulario de búsqueda de un jqGrid
     * @param type $$param
     * @return string
     */
    public function getWhere($param) {
        $where = "";
        if ($param['_search'] == 'true') {
            $json = json_decode(stripslashes($param['filters']));
            $where = " WHERE" . self::getClausulaWhere($json);
        }
        return $where;
    }

    private function getClausulaWhere($json) {
        foreach ($json->rules as $g) {
            $constraint = $json->groupOp;
            if (isset($where)) {  // no inicializar, fallaría el algoritmo
                $where .= " $constraint ";
            } else {
                $where = " ";
            }
            if ($g->op == "eq") {
                $where .= $g->field . " = '$g->data'";
            } elseif ($g->op == "ne") {
                $where .= $g->field . " <> '$g->data'";
            } elseif ($g->op == "lt") {
                $where .= $g->field . " < '$g->data'";
            } elseif ($g->op == "le") {
                $where .= $g->field . " <= '$g->data'";
            } elseif ($g->op == "gt") {
                $where .= $g->field . " > '$g->data'";
            } elseif ($g->op == "ge") {
                $where .= $g->field . " >= '$g->data'";
            } elseif ($g->op == "bw") { // empieza por
                $where .= $g->field . " ILIKE '$g->data%'";
            } elseif ($g->op == "bn") {// no empieza por
                $where .= $g->field . " NOT ILIKE '$g->data%'";
            } elseif ($g->op == "in") {// incluido entre
                $where .= $g->field . " ILIKE '$g->data'";
            } elseif ($g->op == "ni") {
                $where .= $g->field . " NOT ILIKE '$g->data'";
            } elseif ($g->op == 'ew') {// finaliza con
                $where .= $g->field . " ILIKE '%$g->data'";
            } elseif ($g->op == "en") {// no finaliza con
                $where .= $g->field . " NOT ILIKE '%$g->data'";
            } elseif ($g->op == "cn") {// contiene
                $where .= $g->field . " ILIKE '%$g->data%'";
            } elseif ($g->op == "nc") {// no contiene
                $where .= $g->field . " NOT ILIKE '%$g->data%'";
            }
        }
        if (!isset($where)) {
            $where = '';
        }
        if (isset($json->groups)) {
            $count = count($json->groups);
            for ($i = 0; $i < $count; $i++) {
                if (($tmp = self::getClausulaWhere($json->groups[$i]))) {
                    $where .= " " . $constraint . " " . $tmp;
                }
            }
        }
        return $where;
    }

    /**
     * Un método auxiliar para facilitar la paginación de los registros de un jqGrid
     * @param type $select
     * @param type $where
     * @param type $filasPagina
     * @param type $pagina
     * @param type $indice
     * @param type $tipoOrden
     * @return type
     */
    public function getPaginacion($select, $where, $filasPagina, $pagina, $indice, $tipoOrden) {
        $totalFilas = $this->totalFilas("$select $where");
        $totalPaginas = $totalFilas > 0 ? ceil($totalFilas / $filasPagina) : 0;

        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
        }
        // Calcular la posición de la fila inicial
        $inicio = $filasPagina * $pagina - $filasPagina;

        if ($inicio < 0) {
            $inicio = 0;
        }

        $order = "";
        if ($indice != "") {
            $order = "ORDER BY $indice";
            if ($tipoOrden != "") { // Si descendente o ascendente
                $order = "$order $tipoOrden";
            }
        }
        return [
            'total' => $totalPaginas,
            'page' => $pagina,
            'records' => $totalFilas,
            'otras cláusulas' => "$where $order LIMIT $filasPagina OFFSET $inicio"
        ];
    }

    /**
     * Devuelve la cantidad de registros producto de una consulta de la forma:
     * $count = UtilConexion::totalFilas($sql);
     * @param string $sql Una consulta de la forma: "SELECT count(*) FROM [tabla|vista] [WHERE condicion]"
     * @return int El número de filas obtenido a partir del SELECT
     */
    public function totalFilas($sql) {
        if (($result = $this->pdo->query($sql)) !== false) {
            return $result->fetch(PDO::FETCH_NUM)[0];
        } else {
            return 0;
        }
    }

    /**
     * Devuelve el estado que reporta el motor de base de datos luego de una transacción
     * @param boolean $json TRUE por defecto, para indicar que se devuelve una cadena JSON con el estado,
     * FALSE, devuelve un array asociativo con el estado.
     * @return type Un array asociativo o una cadena JSON con el estado de la ejecución.
     */
    public function getEstado($json = TRUE) {
        //error_log('¡Pilas! ' . print_r($this->pdo->errorInfo(), TRUE));
        if (!($ok = !($this->pdo->errorInfo()[1]))) {
            error_log('¡Pilas! ' . print_r($this->pdo->errorInfo(), TRUE));
        }
        $mensaje = '';
        if (count($errorInfo = explode("\n", $this->pdo->errorInfo()[2])) > 1) {
            $mensaje = substr($errorInfo[0], 8);
        }
        return $json ? json_encode(['ok' => $ok, 'mensaje' => $mensaje]) : ['ok' => $ok, 'mensaje' => $mensaje];
    }

    /**
     * prueba del comportamiento de una transacción
     * @param type $param
     */
    public function pruebaTransaccion($param) {
        extract($param);
        $instrucciones = [
            "INSERT INTO departamento(id, nombre) VALUES ('04', 'aaaaaaaaaaaaaaaaaaaaa')",
            "INSERT INTO departamento(id, nombre) VALUES ('05', 'xxxxxxxxxxxxxxxx')",
            "INSERT INTO departamento(id, nombre) VALUES ('06', 'bbbbbbbbbbbbbbbbbbbb')",
            "INSERT INTO departamento(id, nombre) VALUES ('07', 'bbbbbbbbbbbbbbbbbbbb')",
            "INSERT INTO departamento(id, nombre) VALUES ('11', 'xxxxxxxxxxxxxxxxxxxx')",
            "INSERT INTO departamento(id, nombre) VALUES ('13', 'xxxxxxxxxxxxxxxxxxxx')"
        ];

        if (!$obligarEjecucion) {
            $this->pdo->beginTransaction();
        }
        $mensaje = '';
        foreach ($instrucciones as $sql) {
            $ok = $this->pdo->exec($sql);
            if (!$ok) {
                $mensaje .="Error en $sql\n";
            }
        }

        if ($mensaje) {
            $mensaje = "fallo la insercion de los siguientes registros:\n$mensaje";
            $ok = FALSE;
        }

        if (!$obligarEjecucion) {
            if ($mensaje) {
                $this->pdo->rollBack();
            } else {
                $this->pdo->commit();
            }
        }
        echo json_encode(['ok' => $ok, 'mensaje' => $mensaje]);
    }

    public function getEstadosProduccion() {
        echo json_encode(self::$tipoEstadoProduccion);
    }

}
