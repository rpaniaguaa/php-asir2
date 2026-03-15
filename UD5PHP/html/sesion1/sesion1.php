<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rubén Paniagua 2ºASIR</title>
</head>
<body>
    <?php 
        $nombre="Rubén";
        $edad=19;
        $altura=1.85;
        $esEstudiante=TRUE;
        define("PAIS", "España");
        echo "Hola, me llamo $nombre, tengo $edad años, mido $altura metros y ¿soy estudiante? $esEstudiante y vivo en ".PAIS."<br>";

        $a=15;
        $b=4;
        echo "Suma: ".($a+$b)."<br>";
        echo "Resta: ".($a-$b)."<br>";
        echo "Multiplicación: ".($a*$b)."<br>";
        echo "División: ".($a/$b)."<br>";
        echo "Resto: ".($a%$b)."<br>";
        echo "Potencia: ".($a**$b)."<br>";
    ?>
</body>
</html>