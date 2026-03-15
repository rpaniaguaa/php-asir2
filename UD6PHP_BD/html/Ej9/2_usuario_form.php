<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
</head>
<body>
    <h1>Alta usuarios del sistema</h1>
    <form action="2_usuario.php" method="post">
        Nombre <input type="text" name="name"><br><br>
        Email <input type="text" name="email"><br><br>
        Rol <select name=options>
                <option name="editor">Editor</option>
                <option name="admin">Admin</option>
                <option name="user">Ususario</option>
            </select><br><br>
        <input type="submit" value="Crear usuario">

    </form>
</body>
</html>