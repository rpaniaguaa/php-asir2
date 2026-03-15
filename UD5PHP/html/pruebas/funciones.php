<?php 
    function tiene_permiso($rol){
        return $rol == "admin" || $rol == "tecnico";
    }
?>