<?php
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $edad = $_POST['edad'];
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $genero = $_POST['genero'];
    $aficiones = $_POST['aficiones'] ?? [];
    $pais = $_POST['pais'];

    $errores = [];

    if (empty($nombre) || empty($apellido)){
        $errores[] = "Error: Campo nombre y apellidos no deben estar vacío<br>";
    }

    if ($edad <= 0){
        $errores[] = "Error: Edad  no válida<br>";
    }

    if (!empty($errores)) {
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    } 
    
    else {
        echo "<h3>Datos registrados:</h3><ul>";
        echo "<li>Nombre: $nombre $apellido</li>";
        echo "<li>Edad: $edad</li>";
        echo "<li>Género: $genero</li>";
        echo "<li>País: $pais</li>";
        echo "<li>Aficiones: " . (empty($aficiones) ? "Ninguna" : implode(", ", $aficiones)) . "</li>";
        echo "</ul>";

    }

?>