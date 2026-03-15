<?php

    $logs = [
        ["usuario" => "admin",   "hora" => 9,  "exito" => true],
        ["usuario" => "juan",    "hora" => 2,  "exito" => true],
        ["usuario" => "maria",   "hora" => 14, "exito" => false],
        ["usuario" => "admin",   "hora" => 11, "exito" => true],
        ["usuario" => "invitado","hora" => 23, "exito" => false],
        ["usuario" => "root",    "hora" => 16, "exito" => true]
    ];

    $contador=0;
    $successfull_access=0;

    while ($contador < count($logs)) {
        $hora_formateada = date("g:i", mktime($logs[$contador]['hora'], 0, 0));
        if ($logs[$contador]['exito'] == TRUE && ($logs[$contador]['hora'] >= 9 && $logs[$contador]['hora'] <= 17)){
            echo "Usuario: ".$logs[$contador]['usuario']." Hora: ".$hora_formateada."h -> <a style='color: green'>Acceso normal</a><br>";
            $successfull_access +=1;
        }
        elseif ($logs[$contador]['exito'] == TRUE && ($logs[$contador]['hora'] < 9 || $logs[$contador]['hora'] > 17)){
            echo "Usuario: ".$logs[$contador]['usuario']." Hora: ".$hora_formateada."h -> <a style='color: orange'>Acceso FUERA DE HORARIO</a><br>";
            $successfull_access +=1;

        }
        else{
            echo "Usuario: ".$logs[$contador]['usuario']." Hora: ".$hora_formateada."h -> <a style='color: red'>Acceso FALLIDO</a><br>";
 
        }
        $contador+=1;
    }

    if ($successfull_access > 3){
        echo "Total de accesos exitosos: ".$successfull_access."<br>";
        echo "<a style='color: green'>Seguridad OK</a>";
    }

?>