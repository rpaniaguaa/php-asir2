<?php 
    require 'conexion13.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id=trim($_POST['id'] ?? '');

        if (!filter_var($id, FILTER_VALIDATE_INT)){
            die("Error: ".$id." no es un número");
        }

        $sql="SELECT * FROM software WHERE id=:id";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $sw_id = $stmt->fetch(PDO::FETCH_ASSOC);
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php if($sw_id && $sw_id['id'] == $id): ?>

        <h1>Editando: <?= htmlspecialchars($sw_id['nombre']) ?></h1>

        <form action="actualizar_sw.php" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($sw_id['id']) ?>">
            Nombre del software (Solo lectura):<br>
            <input type="text" value="<?= htmlspecialchars($sw_id['nombre']) ?>" disabled><br><br>

            Versión: <br><input type="text" name="version" value="<?= htmlspecialchars($sw_id['version']) ?>"><br><br>

            Tipo de licencia:<br>
            <select name="options">
                    <option value="GPL">GPL</option>
                    <option value="MIT">MIT</option>
                    <option value="BSD">BSD</option>
                    <option value="Priv">Privativa</option>
            </select><br><br>
            <input type="submit" value="Guardar cambios">
        </form>

    <?php else: ?>
        <p>Error: ID no encontrado</p>
        
    <?php endif; ?>
</body>
</html>
<br><a href="editar_sw_form.php">Volver al formulario</a>