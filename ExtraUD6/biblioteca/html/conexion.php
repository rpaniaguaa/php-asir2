<?php 

$username = 'root';
$password = 'root';
$host     = 'bd';           // Nombre del servicio en docker-compose
$dbname   = 'ud6_extra_bd';
$charset  = 'utf8mb4';      // Soporta tildes, ñ y emojis

// DSN (Data Source Name): cadena que identifica la BD
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Opciones de configuración del objeto PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanza excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // fetch() devuelve array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Prepared statements reales
];

try {
    // Las $options se pasan como TERCER argumento (no cuarto)
    // En tu versión original se definían pero nunca se usaban
    $pdo = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    // Nunca mostrar $e->getMessage() en producción (expone datos del servidor)
    die("Error: no se ha podido conectar con la base de datos.");
}
?>

