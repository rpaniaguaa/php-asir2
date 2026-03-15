<?php
    require 'conexion7.php';
    $sql = "SELECT * FROM procesos ORDER BY pid ASC";
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
        h1 {color: #1dc61d;}
        body {font-family: monospace; background-color: #0b0b0b}
        table { width: 100%; border-collapse: collapse; font-family: sans-serif; }
        th, td { border: 1px solid #a89d9d; padding: 8px; text-align: left; color: white; }
        th { background-color: #464545; color: white;}
        
        .user{color: #ddf500; font-weight:bold;}
        .alert {background-color: #e70808; color: white; font-weight:bold;}
    </style>
</head>
<body>
    <h1>Dashboard: Procesos en ejecución</h1>
    <?php if ($stmt->rowCount() > 0): ?>
        <table>
             <thead>
                <tr>
                    <th>PID</th>
                    <th>Usuario</th>
                    <th>Comando</th>
                    <th>CPU %</th>
                    <th>RAM (MB)</th>
                </tr>
            </thead>

             <tbody>

                <?php while ($servidor = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

                    <?php if ($servidor['cpu_usage'] > 80): ?>
                     <tr class="alert">
                    <?php endif; ?>

                    <td><?= $servidor['pid'] ?><!--Es el equivalente a un echo en php--></td>

                    <?php if ($servidor['usuario'] == 'root'): ?>
                        <td class="user">
                            <?= strtoupper(htmlspecialchars($servidor['usuario'])) ?>
                        </td>;
                    <?php else: ?>
                        <td>
                            <?= htmlspecialchars($servidor['usuario']) ?>
                        </td>;
                    <?php endif; ?>
                 
                        <td><?= htmlspecialchars($servidor['comando']) ?></td>
                        <td><?= htmlspecialchars($servidor['cpu_usage']) ?></td>
                        <td><?= htmlspecialchars($servidor['ram_mb']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
        <?php else: ?> 
            <p class="error">No se encontraron servidores en la base de datos.</p>
        <?php endif; ?>
</body>
</html>