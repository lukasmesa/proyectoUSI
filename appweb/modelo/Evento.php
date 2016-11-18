<?php

/**
 * Utilidades para facilitar la vida con los eventos de FullCalendar
 * REquiere PHP 5.2.0 o superior
 */

// Se generará un error grave de PHP si se intenta utilizar la clase DateTime sin esta definición.
date_default_timezone_set('UTC');  // Universal Time Coordinate

class Evento {

    const TODOS_LOS_DIAS_EXPREG = '/^\d{4}-\d\d-\d\d$/'; // formatea la cadena como "2013-12-29"

    public $titulo;         // el título que muestra un evento
    public $todosLosDias;   // un boolean
    public $inicio;         // a DateTime
    public $fin;            // a DateTime, o null
    public $propiedades = array(); // un array de muchas "otras" propiedades del evento


    /**
     * Crea un objeto Evento con base en un array de id=>valor.
     * Opcionalmente puede forzar la zona horaria de las fechas analizadas .
     * @param type $array Un array asociativo con los datos del evento
     * @param type $timezone Opcionalmente la zona horaria
     */
    public function __construct($array, $timezone = null) {

        $this->titulo = $array['title'];

        if (isset($array['allDay'])) {
            // todos los días se ha especificado de forma explícita
            $this->todosLosDias = (bool) $array['allDay'];
        } else {
            // Define todos los días para fechas que no cumplen con ISO8601
            $this->todosLosDias = preg_match(self::TODOS_LOS_DIAS_EXPREG, $array['start']) &&
                (!isset($array['end']) || preg_match(self::TODOS_LOS_DIAS_EXPREG, $array['end']));
        }

        if ($this->todosLosDias) {
            // Si las fechas se repiten, se analiza UTC para evitar problemas de horario de verano.
            $timezone = null;
        }
        $this->inicio = Utilidades::stringComoDateTime($array['start'], $timezone);
        $this->fin = isset($array['end']) ? Utilidades::stringComoDateTime($array['end'], $timezone) : null;

        // Transferir el resto de propiedades
        foreach ($array as $id => $valor) {
            if (!in_array($id, array('title', 'allDay', 'start', 'end'))) {
                $this->propiedades[$id] = $valor;
            }
        }
    }

    /**
     * Analiza si un evento está dentro del rango determinado
     * @param type $rangoInicio Se asume formato UTC con tiempo 00:00:00.
     * @param type $rangoFin Se asume formato UTC con tiempo 00:00:00.
     * @return type boolean Retorna TRUE si el evento está dentro del rango de fechas
     */
    public function estaDentroDelRango($rangoInicio, $rangoFin) {
        // Normalizar las fechas de los eventos
        $inicioEvento = Utilidades::fechaUTC($this->inicio);
        $finEvento = isset($this->fin) ? Utilidades::fechaUTC($this->fin) : null;

        if (!$finEvento) {
            // ¿Sin fecha final? sólo comprobar si el inicio está dentro del rango
            return $inicioEvento < $rangoFin && $inicioEvento >= $rangoInicio;
        } else {
            // Comprobar si la fecha está dentro del rango
            return $inicioEvento < $rangoFin && $finEvento >= $rangoInicio;
        }
    }

    /**
     * Convierte de nuevo el objeto Evento en un array asociativo, que se utilizará para generar JSON
     * @return type Array Un array asociativo que representa el Evento
     */
    public function comoArray() {

        // Se cambian las propiedades básicas. No se preocupe PHP conservará el resto del array intacto
        $array = $this->propiedades;

        $array['title'] = $this->titulo;

        // Definir el formato de fecha
        if ($this->todosLosDias) {
            $format = 'Y-m-d'; // un formato como "2013-12-29"
        } else {
            $format = 'c'; // formato ISO8601 completo, como "2013-12-29T09:00:00+08:00"
        }

        // Serializar fechas en cadenas
        $array['start'] = $this->inicio->format($format);
        if (isset($this->fin)) {
            $array['end'] = $this->fin->format($format);
        }
        return $array;
    }

}
?>