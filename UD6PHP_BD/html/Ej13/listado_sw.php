<?php 
    require 'conexion13.php';
    $sql="SELECT * FROM software";
    $stmt=$pdo->query($sql);
    $software= $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Software</title>
        <style>
        body {font-family: monospace; padding:20px;}
        table { width: 100%; border-collapse: collapse; font-family: sans-serif; }

        th, td { border: 1px solid #a89d9d; padding: 8px; text-align: left; }

        th { background-color: #f2f2f2; }

    </style>
</head>
<body>
    <h1>Estado del sistema</h1>

    <?php if ($stmt->rowCount() > 0): ?>
        
            <table>
                <thead border="1px solid">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Versión</th>
                    <th>Licencia</th>

                </thead>
                <tbody>
            <?php foreach($software as $s): ?>
                    <tr>
                        <td><?=htmlspecialchars($s['id']) ?></td>
                        <td><?=htmlspecialchars($s['nombre']) ?></td>
                        <td><?=htmlspecialchars($s['version']) ?></td>
                        <td><?=htmlspecialchars($s['licencia']) ?></td>
                    </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
    <?php else: ?>
        <p>No se han encontrado registros</p>
    <?php endif; ?>   
</body>
</html>
<br><a href="editar_sw_form.php">Volver al formulario</a>