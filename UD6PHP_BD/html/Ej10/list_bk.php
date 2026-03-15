<?php 
    // 1. Esto debe ir al principio del todo para evitar el error de "headers already sent"
    ob_start(); 
    require 'conexion10.php';

    function recorrer_arr($arr){
        foreach ($arr as $value) {
            echo $value."<br>";
        }
    }

    $pc = trim($_POST['pc'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $error = $_POST['error'] ?? '';
    $observaciones = trim($_POST['observaciones'] ?? '');
    $errores_php = [];


    if (empty($pc)){
        $errores_php[]="Error: no se a asignado el equipo<br>";
    }

    if (count($errores_php) != 0){
        recorrer_arr($errores_php);
    }

    if ($error == 'error' && empty($observaciones)){
        die("Error: Las copias fallidas deben incluir observaciones<br>");
    }

    else{
        try {
            $sql= "INSERT INTO backups (equipo,tipo,fecha,resultado,observaciones)
                   VALUES (:equipo, :tipo,NOW(), :resultado,:observaciones)";
            
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':equipo' => $pc,
                ':tipo'   => $tipo,
                ':resultado' => $error,
                ':observaciones' => $observaciones
            ]);
            header("Location: copias_listado.php");
            exit();

        } catch (PDOException $e) {
            // Si hay error, vaciamos el búfer para que no intente redirigir y muestre el fallo
            ob_end_clean();
            echo "Error: ".$e->getMessage();
        }
    }
?>
<a href="./form.html">Volver al formulario de copias</a>
