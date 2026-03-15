<?php
    function is_empty($value){
        return trim(empty($value));
    }

    $errors = [];
    $host = trim($_POST['host'] ?? '');
    $bd = trim($_POST['bd'] ?? '');
    $user = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $charset = $_POST['options'];

    if (is_empty($host)){
        $errors[]= "Error: host no especificado";
    }

    if (is_empty($bd)){
        $errors[]= "Error: base de datos no especificada";
    }

    if (is_empty($user)){
        $errors[]= "Error: usuario no especificado";
    }

    if (is_empty($password)){
        $errors[]= "Error: contraseña no introducida";
    }

    foreach ($errors as $error) {
        echo $error."<br>";
    }

    //Configurar DSN

    $dsn="mysql:host=$host;dbname=$bd;charset=$charset";

    $options = [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Devuelve arrays asociativos
        PDO::ATTR_EMULATE_PREPARES   => false,            // Seguridad: Desactiva emulación
    ];

    //Configurar PDO para establecer la conexión:
    try {
        $pdo = new PDO($dsn, $user, $password, $options);
        echo "<h1>Conexión exitosa</h1><br>";
        echo "DSN utilixado: ".$dsn;

    } catch (PDOException $e) {
        echo "<h2>Error</h2>";
        echo $e->getMessage();
    }

    echo "<br><a href='index.html'>Volver</a>";


?>