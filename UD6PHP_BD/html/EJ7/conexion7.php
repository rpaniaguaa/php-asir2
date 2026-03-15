<?php
    $username= trim($_POST['username'] ?? '');
    $password= trim($_POST['password'] ?? '');

    $dsn="mysql:host=bd; dbname=unidad6; charset=utf8mb4";

    $options = [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,            
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        echo "Error: No se ha podido conectar a la BD<br>";
        echo "Usuario o contraseña incorrecta";
    }
?>
