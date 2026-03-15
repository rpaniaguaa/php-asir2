<?php
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    if (empty($nombre) || empty($email) || empty($mensaje)){
        echo "Debes rellenar todos los campos<br>";
    }
    elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "El correo '$email' NO es válido.";
    }
    else{
        echo "Nombre: ".$nombre."<br>";
        echo "Email: ".$email."<br>";
        echo "Mensaje: ".$mensaje."<br>";

    }
    
?>