<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <title>Mi primera página PHP</title>

</head>

<body>

    <h1>Bienvenido</h1>

    <p> <?php echo "¡Hola mundo desde PHP!" ?></p>

    <p>La hora actual es: 
        <?php 
            date_default_timezone_set('Europe/Madrid');
            echo date("H:i:s"); 
        ?>
    </p>

</body>

</html>