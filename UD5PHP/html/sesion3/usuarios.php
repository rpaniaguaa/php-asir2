<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
</head>
<body>
    <?php
        $users = [
            "admin1" => 1,
            "admin2" => 2,
            "admin3" => 3
        ];

        
        foreach ($users as $key => $value) {
            if($value < 2){
                echo "Usuario: ".$key." - Permiso: ".($value+1)."<br>";

            }else{
                echo "Usuario: ".$key." - Permiso: ".$value."<br>";

            }

        }
        echo "<pre>";
        print_r($users);
        echo "</pre>";
    ?>
</body>
</html>