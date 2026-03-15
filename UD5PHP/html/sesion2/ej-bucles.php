<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ej-bucles</title>
</head>
<body>
    <?php
        //ej0
        $i=1;
        while ($i <= 15){
            echo "<ul style='color: red'><li>".($i)."</li></ul>";
            $i++;
        }
        echo "<hr>";

        //ej1
        $i=1;
        while ($i <= 15){
            if (($i % 2) == 0){
                echo "<ul style='color: blue'><li>".($i)."</li></ul>";

            }else{
                echo "<ul style='color: red'><li>".($i)." - Impar</li></ul>";

            }

            $i++;
        }
        echo "<hr>";

        //ej2
        for ($numero=1;$numero <= 6;$numero++){
            echo "<h$numero>Nivel de importancia del log ".($numero)."</h$numero>";
        }

        echo "<hr>";

        //ej3

        for($i=0;$i <= 50;$i+=5){
            if ($i < 10){
                echo "Escaneando puerto de red 800".$i."<br>";
            }else{
                echo "Escaneando puerto de red 80".$i."<br>";
            }
        }

        echo "<hr>";

        //ej4

        for ($i=1; $i <= 100; $i+=3){
            if ($i > 95){
                echo "<p>Estado del volumen".($i)."% <strong style='color:red'>[CRITICAL]: Disco casi lleno</strong><br>";
            }elseif ($i > 80 && $i <= 95) {
                echo "<p>Estado del volumen".($i)."% <strong style='color:orange'>[WARNING]: Poco espacio</strong> <br>";
            }elseif ($i < 80){
                echo "<p>Estado del volumen".($i)."%<br>";

            }
        }

        echo "<hr>";


    ?>
</body>
</html>