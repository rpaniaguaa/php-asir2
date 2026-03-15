<?php

    function estadoServicio($activo){
        if ($activo){
            return "Servicio activo.<br>";
        }else{
            return "Servicio detenido.<br>";
        }
    }

    function mostrarEstado($nombreServicio, $estado){
        echo $nombreServicio." ".estadoServicio($estado);
    }

    echo mostrarEstado("Apache",true);
    echo mostrarEstado("MySQL",false);



?>