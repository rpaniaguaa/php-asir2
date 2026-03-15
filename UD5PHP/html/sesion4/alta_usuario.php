<?php
    include 'funciones_validacion.php';
    $nombre = htmlspecialchars(trim($_POST['user_name']));
    $edad = $_POST['edad'];
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $perfil = $_POST['perfil'] ?? "Sin perfil";
    $services = $_POST['services'] ?? [];
    $departamento = $_POST['departamento'];

    if (! validarTexto($nombre) || ! validarTexto($full_name)){
        echo "Error: no deben haber campos vacios<br>";
    }else{
        echo "Nombre de usuario: ".$nombre."<br>";
        echo "Nombre completo: ".$full_name."<br>";
    }

    if (! validarEdad($edad)){
        echo "Error: Edad no válida<br>";
    }else{
        echo "Edad: ".$edad."<br>";
    }

    if (!validarPerfil($perfil)) {
        echo "Debe seleccionar un perfil válido.<br>";
    }else{
        echo "Perfil: ".$perfil."<br>";
    }

    if (!validarDepartamento($departamento)) {
        echo "Debe seleccionar un departamento.<br>";
    }else{
        echo "Departamento: ".$departamento."<br>";
    }
?>