<?php
/**
 * Created by PhpStorm.
 * User: trisb
 * Date: 13/11/2016
 * Time: 11:15
 */

class Grupo {

    // completar con funcionalidad de cliente

    public function getSelect($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un grupo</option>";
        foreach ($conexion->getPDO()->query("SELECT grupo FROM pruebas ORDER BY grupo") as $fila) {
            $select .= "<option value='{$fila['grupo']}'>{$fila['grupo']}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }

}