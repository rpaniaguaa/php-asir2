<?php

$host = "bd";

$user = "usuario_ud6";

$pass = "pwd_ud6";

$db   = "unidad6";

 

$conexion = new mysqli($host, $user, $pass, $db);

 

if ($conexion->connect_error) {

    die("❌ Error de conexión: " . $conexion->connect_error);

}

 

echo "✅ Conexión correcta a la base de datos";

?>