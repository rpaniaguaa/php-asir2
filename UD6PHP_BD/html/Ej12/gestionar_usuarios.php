<?php 
    require 'conexion12.php';
    $sql="SELECT id, nombre, email, rol FROM usuarios";
    $stmt=$pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion usuarios Ej12</title>
</head>
<body>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        
            <?php foreach( $usuarios as $usuario): ?>
              <tr>  
                <td><?= htmlspecialchars($usuario['id']) ?></td>
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['rol']) ?></td>
                <td>
                    <form action="procesar_eliminacion.php" method="post">
                        <?php $esAdmin = ($usuario['rol'] === 'admin') ?>
                        <input type="submit" <?= $esAdmin ? 'disabled title="No se pueden borrar administradores"' : ''?> onclick="return confirm('¿Estás seguro de borrar?')" value="<?= $esAdmin ? "Protejido" : "Eliminar registro" ?>">
                        <input type="hidden" name="id_usr" value="<?= htmlspecialchars($usuario['id']) ?>">
                    </form>
                </td>
              </tr>
            <?php endforeach; ?>
</body>
</html>