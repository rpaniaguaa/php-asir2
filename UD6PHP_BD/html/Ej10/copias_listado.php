<?php
    require 'conexion10.php';
    $sql="SELECT * FROM backups ORDER BY ID ASC";
    $stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BACKUPS</title>
    <style>
        body {font-family: 'Arial';}
        .error { color: red; font-weight: bold;}
        thead {background-color: #64BD00; }
        thead th {color:white;}
        th, td { border: 1px solid #a89d9d;padding: 10px; text-align: left; }

    </style>
</head>
<body>
    <h1>Listado de copias de seguridad</h1>
    <?php if ($stmt ->rowCount() > 0): ?>
        <table border="1px solid">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Equipo</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Resultado</th>
                    <th>observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($servidor = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($servidor['id']) ?></td>
                        <td><?= htmlspecialchars($servidor['equipo']) ?></td>
                        <td><?= htmlspecialchars($servidor['tipo']) ?></td>
                        <td><?= htmlspecialchars($servidor['fecha']) ?></td>
                        <td><?= htmlspecialchars($servidor['resultado']) ?></td>
                        <td><?= htmlspecialchars($servidor['observaciones']) ?></td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="error">No se han encontrado backups</p>
    <?php endif; ?>
    <br><a href="./form.html">Volver al formulario de copias</a>

</body>
</html>