<?php 
    include 'funciones.php';
    $pacientes = [
        "Juan" => ["altura" => 1.75, "peso" => 70],
        "Ana"  => ["altura" => 1.62, "peso" => 60],
        "Luis" => ["altura" => 1.80, "peso" => 85],
        "Marta"=> ["altura" => 1.68, "peso" => 100]
    ];
    echo "<h1>Datos de Luís:</h1>";
    echo number_format($pacientes["Juan"]["altura"],2,",")."<br>";
    echo number_format($pacientes["Juan"]["peso"],2,",")." kg";
    $a = rand(1,15);
    echo "<br>".$a."<br>";

    foreach ($pacientes as $key => $value){
        echo $key." mide ".number_format($pacientes[$key]["altura"],2,",")."m<br>";
    }

    $media=0;
    foreach ($pacientes as $key => $value) {
        $media +=$pacientes[$key]["peso"];
    }

    echo "<br>Peso medio: ".($media/=count($pacientes));

    echo ($media < 100) ? " Peso bueno": " Peso malo";

    $usuarios = [

        ["nombre" => "root", "rol" => "admin"],

        ["nombre" => "juan", "rol" => "tecnico"],

        ["nombre" => "invitado", "rol" => "usuario"],

        ["nombre" => "pepe", "rol" => "usuario"]

    ];
    echo "<br><hr>";
    echo $usuarios[0]["nombre"];

    
    echo (tiene_permiso($usuarios[0]["rol"])) ? " Acceso denegado" : "Acceso permitido";

    echo "<br><hr>";

    $texto = "   admin@servidor.com    ";

    echo strlen($texto)."<br>";        // Longitud → 10
    echo strtolower($texto)."<br>";    // convierte a minúsculas
    echo strtoupper($texto)."<br>";    // convierte a mayúsculas
    echo substr($texto, 0, 7)."<br>";  // Caracteres entre el 0 y 7
    echo trim($texto)."<br>";         // Elimina espacios al inicio y fin
    echo strpos($texto, "@")."<br>";  // Devuelve posición de la cadena "M"
    $vector = explode("@", $texto);  // Convierte la cadena $texto en un array usando el separador "@"
    print_r($vector);                 // Muestra el array resultante
    echo "<br>";
    echo implode("##", $vector);     // Contenido del array $vector como un string usando "##" como separador
    echo "<br>";// Remplaza cadena “servidor” por “midominio” en $texto
    echo str_replace("servidor", "midominio", $texto)."<br>"; 

    echo "<br><hr>";

    $frutas = ["manzana", "banana", "cereza"];
    print_r($frutas);                      // Muestra el array completo
    array_push($frutas, "naranja");        // Añade "naranja" al final del array
    array_pop($frutas);                   // Elimina el último elemento del array
    sort($frutas);                        // Ordena el array alfabéticamente
    rsort($frutas);                       // Ordena el array en orden inverso

    $clave = array_search("banana", $frutas); // Busca la posición de "banana"
    echo $clave;                          // Muestra la posición encontrada
    $verduras = ["p1"=> "lechuga", "a2"=> "zanahoria", "d4"=>"pepino"];
    asort($verduras);                       // Ordena el array por valores
    ksort($verduras);                      // Ordena el array por claves

    echo "<br><hr>";

    date_default_timezone_set('Europe/Madrid')."<br>"; // Establece la zona horaria        
    echo date("Y-m-d H:i:s")."<br>";        // Fecha y hora actual → 2024-06-15 14:30:00
    echo date("d/m/Y")."<br>";             // Fecha actual en formato día/mes/año → 15/06/2024
    echo date("l")."<br>";                 // Día de la semana → Saturday
    echo date("F")."<br>";                 // Mes actual → June
    echo time()."<br>";                    // Marca de tiempo Unix actual → 1623761400
    echo date("Y-m-d H:i:s", time() + 3600)."<br>"; // Fecha y hora dentro de una hora
?>