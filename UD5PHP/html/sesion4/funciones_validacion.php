<?php
    function validarEdad($edad){
        return $edad > 0;
    }

    function validarTexto($texto){
        return ! empty($texto);
    }

    function validarPerfil($perfil) {
        $perfilesPermitidos = ["admin", "tecnico", "usuario"];
        return in_array($perfil, $perfilesPermitidos);
    }

    function validarDepartamento($departamento) {
        return $departamento !== "";
    }

?>