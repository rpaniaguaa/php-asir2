<?php 
    require 'conexion11.php';

    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    if (! $id){
        die("Error: id no válido");
    }
    try{
        echo $_POST['id_usuario']."<br><br>";

        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0){
            echo "<h3 style='color:green;'>Usuario eliminado correctamente</h3>";
            echo "Se ha eliminado el usuario con ID: ".$id."<br>";
            echo "Número de registros eliminados: ".$stmt->rowCount()."<br>";
        }else{
            echo "No se encontró usuario con ese ID<br>";
        }
    }catch (PDOException $e) {

        error_log("Error borrando: " . $e->getMessage());
        die("No se pudo eliminar el registro.");

    }
?>
<a href="form_eliminar.php">Volver al formulario</a>