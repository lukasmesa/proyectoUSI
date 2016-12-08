<?php

/**
 * Librería de funciones varias que requiere la aplicación
 */
class Utilidades {
    // Utilidades para el manejo de fechas

    /**
     * Analiza una cadena que representa una fecha y la convierte en un objeto DateTime
     * opcionalmente con zona horaria
     * @param type $string La Cadena que representa la fecha
     * @param type $timezone Opcionalmente la zona horaria
     * @return \DateTime
     */
    public static function stringComoDateTime($string, $timezone = null) {
        $date = new DateTime($string, $timezone ? $timezone : new DateTimeZone('UTC'));  // UTC = Universal Time Coordinate
        if ($timezone) {
            // Forzar la zona horaria si fue ignorada
            $date->setTimezone($timezone);
        }
        return $date;
    }

    /**
     * Toma los valores año/mes/día del DateTime dado y los convierte a un nuevo DateTime, pero como UTC
     * @param type $datetime
     * @return \DateTime
     */
    public static function fechaUTC($datetime) {
        return new DateTime($datetime->format('Y-m-d'));
    }

    /**
     * Permite descargar un archivo
     * @param type $param un array asociativo con el elemento 'archivo' que contiene el nombre del archivo a descargar
     * @throws Exception Se lanza un error si el archivo no se encuentra disponible.
     */
    public static function descargar($param) {
        extract($param);
        try {
            // en la linea siguiente debe unirse la ruta de descarga al nombre de archivo y no se debe tocar nada más
            $rutaArchivo = RUTA_DESCARGA . $archivo;
            if (!is_file($rutaArchivo)) {
                error_log("Problemas descargando $$rutaArchivo");
                throw new Exception("El archivo $archivo no se encuentre disponible");
            } else {
                header('Set-Cookie: fileDownload=true; path=/');  // < ******************************** OJO
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Disposition: attachment; filename=\"$archivo\"\n");  // Oculta la ruta de descarga y permite espacios en nombres de archivos
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . filesize($rutaArchivo));
                @readfile($rutaArchivo);
            }
        } catch (Exception $e) {
            echo json_encode(['ok' => 0, 'mensaje' => $e->getMessage()]);  // Este mensaje no soporta formateo del html incluso usando htmlspecialchars()
        }
    }
}
?>