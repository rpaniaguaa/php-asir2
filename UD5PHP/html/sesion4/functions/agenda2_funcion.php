<?php

    function mostrarAgenda($agenda){
        echo "<table border='1'>";
        echo "<tr><th>Nombre</th><th>email</th><th>teléfono</th></tr>";
        foreach ($agenda as $user) { 

            echo "<tr>";
            echo "<td>".$user["nombre"]."</td>";
            echo "<td>".$user["email"]."</td>";
            echo "<td>".$user["telefono"]."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
?>