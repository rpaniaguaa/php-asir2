<?php
    require 'conexion11.php';
    $sql="SELECT id, nombre, email, rol FROM usuarios";
    $stmt=$pdo->query($sql);
?>



<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Borrar servidor</title>
    </head>

    <body>
        <?php if ($stmt-> rowCount() > 0): ?>
            <h1>Eliminar Usuarios del sistema</h1>
            <hr>
            <h2>Usuarios registrados</h2>
            <table border="1px solid">
                <thead>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                </thead>
                <tbody>
                    <?php while ($servidor = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($servidor['id'])?></td>
                        <td><?= htmlspecialchars($servidor['nombre'])?></td>
                        <td><?= htmlspecialchars($servidor['email'])?></td>
                        <td><?= htmlspecialchars($servidor['rol'])?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        <?php else: ?>
            <p>Error: no se han encontrado registros</p>
        <?php endif; ?>
        <hr>
        <h1>Formulario de eliminación</h1>
        <form action="eliminar_usuario.php" method="post">
            <p>
                <label for="id">ID del servidor a borrar</label><br />
                <input type="number" id="id" name="id" required />
                <input type="hidden" name="id_usuario" value="Valor oculto">
            </p>

            <p>
                <button type="submit" onclick="return confirm('¿Estás seguro de borrar?')">Eliminar usuario</button>
            </p>

        </form>

    </body>

</html>