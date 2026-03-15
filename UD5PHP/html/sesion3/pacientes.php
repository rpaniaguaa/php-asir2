<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
        #Ej 1 y 2
        $pacientes = [
            "Juan" => ["altura" => 1.75, "peso" => 70],
            "Ana"  => ["altura" => 1.62, "peso" => 60],
            "Luis" => ["altura" => 1.80, "peso" => 85],
            "Marta"=> ["altura" => 1.68, "peso" => 100]
        ];
        echo "<h1>Datos de Juan</h1>";
        echo "Altura: ".($pacientes["Juan"]["altura"])."<br>";
        echo "Peso: ".($pacientes["Juan"]["peso"])."<br>";
        echo "<hr>";
        #Ej3
        echo "<h1>Nombres y alturas</h1>";

        foreach ($pacientes as $key => $value) {
            echo $key." mide ".number_format($value["altura"],2)." m<br>";
        }
        
        echo "<hr>";
        #Ej4
        echo "<h1>Peso medio</h1>";
        $suma = 0;
        foreach ($pacientes as $key => $value) {
            $peso = number_format($value["peso"],2);
            $suma+=$peso;
        }
        $media = $suma / count($pacientes);
        echo "El peso medio es: ".$media." kg<br>";

        foreach ($pacientes as $key => $value) {
            $peso = number_format($value["peso"],2);

            if ($media < $peso){
                echo $key." está por encima del peso calculado, Su peso: ".$peso." kg<br>";
                $nom=$key;
                $arr[] = $nom;

            }
        }
        print_r($arr);
    ?>

</body>
</html>