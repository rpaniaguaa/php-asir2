<?php
    require 'conexion8.php';

    $tech = $_POST['tech'] ?? '';
    $tipo = $_POST['options'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    try {

        $sql = "INSERT INTO incidencias (fecha,tecnico,tipo,descripcion)
                VALUES (NOW(), :tecnico, :tipo, :descripcion)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':tecnico' => $tech,
            ':tipo'   => $tipo,
            ':descripcion' => $descripcion
        ]);

        $filasInsertadas = $stmt->rowCount();
        echo "Incidencia enviada con éxito. ID Asignada: " . $pdo->lastInsertId();

    }catch (PDOException $e) {

        if ($e->getCode() == 23000) {
            echo " Error: Esta incidencia ya está registrado.";
        }   else {
            error_log("Error crítico DB: " . $e->getMessage());
            echo $e->getMessage()." Error del sistema. Contacte al administrador.";
        }
    }

?>