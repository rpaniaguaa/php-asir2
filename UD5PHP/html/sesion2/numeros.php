<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Numeros.php</title>
</head>
<body>
    <?php
        /*
        1.Muestra todos los números pares del 2 al 50 usando un bucle for.
        2.Muestra los múltiplos de 5 del 5 al 100 usando un bucle while.
        3.Usando un bucle do...while, muestra la cuenta atrás desde 10 hasta 1.
        */

        for ($i=2; $i <= 50;$i+=2){
            echo $i." ";
        }
        echo "<hr>";

        $i=5;
        while ($i <= 100){
            echo $i." ";
            $i+=5;
        }
        echo "<hr>";

        $i=10;
        do{
            echo $i." ";
            $i--;
            
        } while($i >= 1);
    ?>    
</body>
</html>