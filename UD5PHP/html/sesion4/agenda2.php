<?php
    include './functions/agenda2_funcion.php';
    $agenda = [
        ["nombre" => "Ana", "email" => "ana@mail.com", "telefono" => "600111222"],
        ["nombre" => "Luis", "email" => "luis@mail.com", "telefono" => "600333444"],
        ["nombre" => "Marta", "email" => "marta@mail.com", "telefono" => "600555666"],
        ["nombre" => "Jose", "email" => "jose@mail.com", "telefono" => "600666666"],
        ["nombre" => "carla", "email" => "carla@mail.com", "telefono" => "600666777"]
    ];
    mostrarAgenda($agenda);
    echo "<h3>Total de usuarios: ".(count($agenda))."</h3>";
    echo "<h3>".date("d/m/Y H:i")."</h3>";
?>