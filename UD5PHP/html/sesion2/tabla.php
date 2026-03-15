<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tabla.php</title>
</head>
<body>
    <?php
        $numFilas=10;

        echo "<table border='1' cellpadding='5'>";

        for ($i = 1; $i <= $numFilas; $i++) {    

            echo "<tr><td> 7 x ".($i)." = ".(7*$i)."</td></tr>";

        }

        echo "</table>";
    ?>
</body>
</html>