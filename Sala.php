<?php
/**
 * Created by PhpStorm.
 * User: trisb
 * Date: 13/11/2016
 * Time: 12:33
 */
class Sala {

    // completar con funcionalidad de cliente

    public function getSelect($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione una sala</option>";
        foreach ($conexion->getPDO()->query("SELECT sala FROM pruebas ORDER BY sala") as $fila) {
            $select .= "<option value='{$fila['sala']}'>{$fila['sala']}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }

}