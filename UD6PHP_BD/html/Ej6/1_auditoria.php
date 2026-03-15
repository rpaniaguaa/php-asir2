<?php
    require 'conexion.php';
    $sql = "SELECT * FROM software ORDER BY id ASC";
    $stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria</title>
    <meta charset="UTF-8">

    <title>Inventario de Servidores</title>

    <style>
        body {font-family: monospace; padding:20px;}
        table { width: 100%; border-collapse: collapse; font-family: sans-serif; }

        th, td { border: 1px solid #a89d9d; padding: 8px; text-align: left; }

        th { background-color: #f2f2f2; }

        .error { color: red; font-weight: bold; }

    </style>
</head>
<body>
    <h1>Reporte de licencias instaladas</h1>

 

    <?php if ($stmt->rowCount() > 0): ?>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Software</th>
                    <th>Versión</th>
                    <th>Tipo Licencia</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($servidor = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

                    <tr>

                        <td><?= $servidor['id'] ?></td>
                        <td><?= htmlspecialchars($servidor['nombre']) ?></td>
                        <td><?= htmlspecialchars($servidor['version']) ?></td>
                        <td><?= htmlspecialchars($servidor['licencia']) ?></td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    <?php else: ?>

        <p class="error">No se encontraron servidores en la base de datos.</p>

    <?php endif; ?>
</body>
</html>