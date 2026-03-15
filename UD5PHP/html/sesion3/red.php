<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>red</title>
</head>
<body>
    <h1>Configuración de interfaces</h1>
    <?php
        $red_arr = [
            ["192.168.1.1", "255.255.255.0", "192.168.1.254"],
            ["10.0.0.1","255.255.0.0","10.0.0.254"],
            ["172.16.0.1","255.255.0.0","172.16.0.254"],
            ["192.168.100.1", "255.255.255.0", "192.168.100.254"]
        ];
        
        for ($i=0; $i < count($red_arr); $i++) { 
            echo "<h2>RED".($i+1)."</h2>";

            echo "IP: ".$red_arr[$i][0]."<br>";
            echo "Máscara: ".$red_arr[$i][1]."<br>";
            echo "Gateway: ".$red_arr[$i][2]."<br>";

            $ip = $red_arr[$i][0];
            $punto = strpos($ip,".");
            $oct1 = substr($ip,0,$punto);

            if ($oct1 == "192"){
                echo "<strong> → Red local (Privada)</strong>";
            }

            echo "<hr>";
        }

    ?>

</body>
</html>