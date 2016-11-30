<?php
/**
 * Created by PhpStorm.
 * User: trisb
 * Date: 13/11/2016
 * Time: 11:15
 */

class usuario {

    // completar con funcionalidad de cliente

    public function getSelectUsuario($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un usuario</option>";
        foreach ($conexion->getPDO()->query("SELECT id_usuario FROM usuario ORDER BY usuario") as $fila) {
            $select .= "<option value='{$fila['id_usuario']}'>{$fila['id_usuario']}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }

}