<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variables php</title>
</head>
<body>
    <?php  
        $nombre = "Ruben";
        $edad= 19;
        $ciudad = "Valencia";
        echo "Me llamo $nombre, tengo $edad años y vivo en $ciudad";

        echo "<br>";

        define("PI",3.14);
        $radio = 5;
        $area=number_format(PI*($radio**2),2);
        echo "El área del círculo es: $area";

        echo "<br>";

        $precio=560;
        $descuento=90;    
        echo "<p>Precio original: $precio €</p>";
        echo "<p>Descuento: $descuento €</p>";
        $PVP = $precio - $descuento;
        echo "El PVP es $PVP";

    ?>
</body>
</html>