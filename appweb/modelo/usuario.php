<?php
/**
 * Created by PhpStorm.
 * User: trisb
 * Date: 13/11/2016
 * Time: 11:15
 */

class usuario {

    // completar con funcionalidad de cliente
    public function autenticar($param){
        extract($param);
        $estado=false;
        foreach ($conexion->getPDO()->query("SELECT administrativo.id_usuario, usuario.contrasena FROM usuario, administrativo WHERE administrativo.id_usuario=usuario.id_usuario") as $fila) {
                if($fila['id_usuario']==$id_usuario && $fila['contrasena']==$contrasena){
                    $estado=true;
                }
        }
        echo json_encode($estado);
    }

    public function getSelectUsuario($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un usuario</option>";
        foreach ($conexion->getPDO()->query("SELECT id_usuario, nombre, apellido FROM usuario ORDER BY apellido") as $fila) {
            $name = $fila['nombre'].' '.$fila['apellido'];
            $select .= "<option value='{$fila['id_usuario']}'>{$name}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }
	
	public function getSelectUsuario2($param) {
        $json = FALSE;
        extract($param);
        $select = "";
        $select .= "<option value='0'>Seleccione un usuario</option>";
        foreach ($conexion->getPDO()->query("SELECT id_usuario, nombre, apellido FROM usuario ORDER BY apellido") as $fila) {
            $name = $fila['id_usuario'].' '.$fila['nombre'].' '.$fila['apellido'];
            $select .= "<option value='{$fila['id_usuario']}'>{$name}</option>";
        }
        echo $json ? json_encode($select) : ("<select id='$id'>$select</select>");
    }
}
