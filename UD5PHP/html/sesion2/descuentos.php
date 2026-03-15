<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
        $edad=35;

        if ($edad < 12){
            $descuento=50;

        }elseif ($edad >= 12 && $edad <= 24){
            $descuento=25;

        }elseif ($edad >= 25 && $edad <= 64){
            $descuento=10;

        }elseif ($edad >= 65){
            $descuento=40;

        }

        echo "Edad: $edad. Descuento: $descuento%<br>";

        switch ($descuento) {
            case 50:
                echo "Descuento especial para niños";
                break;
            
            case 25:
                echo "Descuento especial para adolescentes";
                break;
            
            case 10:
                echo "Descuento especial para adultos";
                break;
            
            case 40:
                echo "Descuento especial para mayores";
                break;

        }
    ?>
</body>
</html>