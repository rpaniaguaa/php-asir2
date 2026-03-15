<?php
    function tienePermiso($rol){
        return $rol == "admin" || $rol == "tecnico";
    }

    function mensajeAcceso($usuario, $permitido){
        if ($permitido) {
            return "Usuario ".$usuario.": acceso permitido<br>";
        }else{
            return "Usuario ".$usuario.": acceso denegado<br>";
        }
    }
?>