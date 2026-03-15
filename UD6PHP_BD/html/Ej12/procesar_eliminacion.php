<?php 
    require 'conexion12.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_usr=$_POST['id_usr'];

        $sql="DELETE FROM usuarios WHERE id=:id";
        $stmt=$pdo->prepare($sql);

        $stmt->execute([':id' => $id_usr]);

        if ($stmt->rowCount() > 0){
            echo "<h3 style='color:green;'>Usuario eliminado correctamente</h3>";
            echo "Se ha eliminado el usuario con ID: ".$id_usr."<br>";
        }else{
            die("Error: no se ha podido eliminar al usuario");
        }

    }else{
        die("Acceso denegado");
    }

?>
<a href="gestionar_usuarios.php"> Volver a gestionar usuarios</a>