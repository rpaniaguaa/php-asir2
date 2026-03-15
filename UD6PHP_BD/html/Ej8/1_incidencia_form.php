<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
</head>
<body>
    <h1>Formulario de incidencias</h1>
    <form action="1_incidencia.php" method="post">
            Técnico <input type="text" name="tech"><br><br>
            Tipo de incidencia 
            <select name="options">
                <option value="sistema">Sistema</option>
                <option value="seguridad">Seguridad</option>
                <option value="hardware">Hardware</option>
            </select><br><br>

            <label for="descripcion">Descripción:</label><br>
            <textarea id="descripcion" name="descripcion" rows="4" cols="50" placeholder="Escribe aquí los detalles..."></textarea>
            <br><br><button>Enviar</button>

    </form>
</body>
</html>