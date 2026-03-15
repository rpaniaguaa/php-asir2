<?php
    include './functions/acceso_funciones.php';

    $usuarios = [
        ["nombre" => "root", "rol" => "admin"],
        ["nombre" => "juan", "rol" => "tecnico"],
        ["nombre" => "invitado", "rol" => "usuario"],
        ["nombre" => "pepe", "rol" => "usuario"]
    ];

    for ($i=0; $i < count($usuarios) ; $i++) {      
        echo mensajeAcceso($usuarios[$i]["nombre"],tienePermiso($usuarios[$i]["rol"]));
        
    }

?>