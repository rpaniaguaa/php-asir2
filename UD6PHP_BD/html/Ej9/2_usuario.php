<?php

    require 'conexion9.php';

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['options'];
    $errores=[];

    if ( ! filter_var($email,FILTER_VALIDATE_EMAIL)){
         $errores[]="Error: Email no válido<br>";
    }
    
    if (empty($name)){
        $errores[]="Error:No se ha asignado un nombre válido al usuario<br>";
    }

    if (count($errores) != 0){
        foreach ($errores as $error) {
            echo $error."<br>";
        }
    }
    else{
        try {
            $sql="INSERT INTO usuarios (nombre,email,rol)
                  VALUE(:nombre,:email,:rol)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nombre' => $name,
                ':email' => $email,
                ':rol' => $rol,
            ]);
            echo "Usuario ".$name." creado con éxito.<br>";

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo " Error: El email ya está registrado.";
            }    
        }
    }
?>