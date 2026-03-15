<?php
    $pacientes = [

    "Juan" => ["altura" => 1.75, "peso" => 70],
    "Ana"  => ["altura" => 1.62, "peso" => 60],
    "Luis" => ["altura" => 1.80, "peso" => 85],
    "Marta"=> ["altura" => 1.68, "peso" => 100]
    ];
    echo "<table border=1px>";
    echo "<thead>";
        echo "<tr>";
            echo "<th>Nombre</th>";
            echo "<th>Altura</th>";
            echo "<th>Peso</th>";       
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($pacientes as $key => $value) {
        echo "<tr>";
        echo "<td>".$key."</td>";
        echo "<td>".$value["peso"]."</td>";
        echo "<td>".$value["altura"]."</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    echo "<br>";
    $c="20";
    $a=20;
    echo $c+5;
    echo "<br>";
    echo (var_dump($c))."<br>";
    echo (var_dump($a))."<br>";
    echo ($c === $a);//falso (nada) - true (1)
?>