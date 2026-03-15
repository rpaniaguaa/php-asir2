

<?php

// 1. Credenciales (En producción esto iría en variables de entorno o un archivo .env)

$host     = 'bd';

$dbname   = 'unidad6';

$username = 'root';

$password = 'pwd_root_ud6'; // Cuidado: En XAMPP está vacía, en producción NUNCA.

 

// 2. Construcción del DSN

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

 

// 3. Configuración de Opciones de PDO

$options = [

    PDO::ATTR_ERRMODE     => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en error

    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Devuelve arrays asociativos

    PDO::ATTR_EMULATE_PREPARES   => false,            // Seguridad: Desactiva emulación

];

 

// 4. Bloque Try / Catch

try {

    // Intentamos conectar

    $pdo = new PDO($dsn, $username, $password, $options);

   

    // Si llegamos aquí, la conexión fue exitosa.

    echo "Conectado"; // (Solo para depurar, comentar en producción)

 

} catch (PDOException $e) {

    // Si falla, capturamos la excepción aquí.

   

    // ¡IMPORTANTE! Nunca mostrar el error real al usuario final.

    echo $e->getMessage(); // <-- ESTO ES UN FALLO DE SEGURIDAD

   

    // Lo correcto: Guardar en log y mostrar mensaje genérico

    error_log("Error de conexión: " . $e->getMessage());

    die("Error crítico: No se pudo conectar a la base de datos.");

}

?>
