<?php
    $username = 'root';
    $password = 'pwd_root_ud6';
    $dsn="mysql:host=bd; dbname=unidad6; charset=utf8mb4";

    $options = [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,            
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        echo "<h1>Conexión exitosa</h1><br>";
        echo "DSN utilixado: ".$dsn;

    } catch (PDOException $e) {
        echo "<h2>Error</h2>";
        echo $e->getMessage();
    }

?>