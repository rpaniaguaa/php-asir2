<?php
// ============================================================
// editar.php — Formulario para editar un libro existente
// Depende de: conexion.php, funciones.php, estilo.css
// Flujo:
//   1. Llega GET con ?id=X → SELECT → mostrar formulario relleno
//   2. Llega POST con datos → validar → UPDATE → mensaje éxito
// ============================================================

require_once 'conexion.php';
require_once 'funciones.php';

$generos = ['Ficción', 'No Ficción', 'Ciencia', 'Historia', 'Tecnología', 'Arte', 'Otros'];

$errores = [];
$exito   = false;
$libro   = [];   // Contendrá los datos actuales del libro (de la BD o del POST)

// ============================================================
// PASO 1: OBTENER Y VALIDAR EL ID
// El id puede llegar por GET (al entrar desde index.php)
// o por POST (al enviar el formulario de edición).
// Usamos el operador ?? para intentar GET primero, luego POST.
// filter_var con FILTER_VALIDATE_INT comprueba que sea entero
// estricto y positivo. Si no es válido, paramos todo.
// ============================================================
$id_raw = $_GET['id'] ?? $_POST['id'] ?? null;

// filter_var devuelve false si no es entero, o el entero si lo es.
// Comparamos === false (estricto) porque filter_var('0') devuelve 0
// que con == false también sería true (trampa clásica).
if (filter_var($id_raw, FILTER_VALIDATE_INT) === false || (int)$id_raw <= 0) {
    // ID inválido: mostramos error y no continuamos
    $id_invalido = true;
} else {
    $id = (int) $id_raw;
    $id_invalido = false;
}

// ============================================================
// PASO 2: CARGAR DATOS DEL LIBRO DESDE LA BD
// Solo si el ID es válido y NO estamos procesando el POST.
// Si estamos en POST, los datos vendrán de $_POST (para
// repoblar el form si hay errores de validación).
// ============================================================
if (!$id_invalido && $_SERVER['REQUEST_METHOD'] !== 'POST') {

    // SELECT con parámetro preparado: nunca concatenamos $id en el SQL
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $libro = $stmt->fetch(); // fetch() devuelve una fila o false si no existe

    // Si el libro no existe en la BD (alguien puso un ID inventado)
    if ($libro === false) {
        $id_invalido = true;
    }
}

// ============================================================
// PASO 3: PROCESAR EL FORMULARIO DE EDICIÓN (POST)
// Solo entramos aquí si el método es POST y el ID es válido.
// ============================================================
if (!$id_invalido && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // ----------------------------------------------------------
    // 3a. RECOGER Y LIMPIAR DATOS DEL POST
    // Igual que en agregar.php: trim + ?? '' para evitar Notice.
    // Guardamos en $libro para repoblar el formulario si hay errores.
    // ----------------------------------------------------------
    $libro = [
        'id'               => $id,
        'titulo'           => trim($_POST['titulo']          ?? ''),
        'autor'            => trim($_POST['autor']           ?? ''),
        'año_publicacion'  => trim($_POST['año_publicacion'] ?? ''),
        'genero'           => trim($_POST['genero']          ?? ''),
        'paginas'          => trim($_POST['paginas']         ?? ''),

        // Checkbox: si no está marcado no llega en POST
        // isset devuelve true/false → (int) lo convierte a 1/0
        'leido'            => isset($_POST['leido']) ? 1 : 0,

        // Valoración solo si está marcado como leído
        'valoracion'       => isset($_POST['leido']) ? (trim($_POST['valoracion'] ?? '')) : null,
    ];

    // ----------------------------------------------------------
    // 3b. VALIDAR CON LA FUNCIÓN DE funciones.php
    // Reutilizamos validarLibro() exactamente igual que en agregar.php
    // ----------------------------------------------------------
    $resultado = validarLibro($libro);

    if (!$resultado['valido']) {
        $errores = $resultado['errores'];

    } else {
        // ----------------------------------------------------------
        // 3c. UPDATE CON PREPARED STATEMENT
        // Actualizamos todos los campos editables.
        // El WHERE id = :id es CRÍTICO: sin él actualizaríamos
        // TODOS los registros de la tabla (error catastrófico).
        // ----------------------------------------------------------

        // Convertimos valoración vacía a null para MySQL
        $valoracion_final = ($libro['valoracion'] !== '' && $libro['valoracion'] !== null)
                            ? (int) $libro['valoracion']
                            : null;

        $sql = "UPDATE libros SET
                    titulo              = :titulo,
                    autor               = :autor,
                    `año_publicacion`   = :anno_publicacion,
                    genero              = :genero,
                    paginas             = :paginas,
                    leido               = :leido,
                    valoracion          = :valoracion
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo'             => $libro['titulo'],
            ':autor'              => $libro['autor'],
            ':anno_publicacion'   => (int) $libro['año_publicacion'],
            ':genero'             => $libro['genero'],
            ':paginas'            => (int) $libro['paginas'],
            ':leido'              => $libro['leido'],
            ':valoracion'         => $valoracion_final,
            ':id'                 => $id,
        ]);

        // ----------------------------------------------------------
        // rowCount() devuelve el número de filas afectadas por el UPDATE.
        // Puede ser 0 si los datos enviados son idénticos a los de la BD
        // (MySQL no ejecuta el UPDATE si no hay cambios reales).
        // En ambos casos (0 o 1) consideramos la operación exitosa
        // porque no hubo error PDO (si hubiera fallado, lanzaría excepción).
        // ----------------------------------------------------------
        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✏️ Editar libro — Mi Biblioteca</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<header>
    <h1>📚 Mi <span>Biblioteca</span></h1>
    <a href="index.php" class="btn-agregar">← Volver al listado</a>
</header>

<div class="container">
<div class="form-card">

    <!-- ============================================================
         CASO A: ID INVÁLIDO O LIBRO NO ENCONTRADO
         Mostramos error y enlace de vuelta. No renderizamos el form.
         ============================================================ -->
    <?php if ($id_invalido): ?>

        <div class="alerta alerta-error">
            ❌ El libro solicitado no existe o el ID no es válido.
        </div>
        <div class="form-botones">
            <a href="index.php" class="btn-submit">← Volver al listado</a>
        </div>

    <!-- ============================================================
         CASO B: UPDATE EXITOSO
         Mostramos confirmación con opciones de navegación.
         ============================================================ -->
    <?php elseif ($exito): ?>

        <div class="alerta alerta-exito">
            ✅ El libro <strong>"<?= htmlspecialchars($libro['titulo']) ?>"</strong>
            ha sido actualizado correctamente.
        </div>
        <div class="form-botones">
            <a href="index.php" class="btn-submit">📚 Ver listado</a>
            <a href="editar.php?id=<?= $id ?>" class="btn-volver">✏️ Seguir editando</a>
        </div>

    <!-- ============================================================
         CASO C: MOSTRAR EL FORMULARIO
         Tanto en la carga inicial (GET) como si hubo errores (POST).
         $libro contiene los datos de la BD (GET) o del POST (errores).
         ============================================================ -->
    <?php else: ?>

        <h2>✏️ Editando: <em style="color:#e2b96f"><?= htmlspecialchars($libro['titulo']) ?></em></h2>

        <!-- Lista de errores de validación (solo si los hay) -->
        <?php if (!empty($errores)): ?>
            <ul class="lista-errores">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- ============================================================
             FORMULARIO DE EDICIÓN
             method="POST" al mismo archivo.
             El id viaja en un input hidden para que el PASO 3 lo reciba.
             Los value están pre-rellenados con los datos de $libro.
             ============================================================ -->
        <form method="POST" action="editar.php">

            <!-- Campo oculto con el ID del libro.
                 Es el que permite saber qué registro actualizar en el UPDATE.
                 No es visible para el usuario pero sí editable con DevTools,
                 por eso validamos el ID en el servidor antes del UPDATE. -->
            <input type="hidden" name="id" value="<?= $id ?>">

            <!-- TÍTULO -->
            <div class="form-grupo">
                <label for="titulo">Título *</label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    value="<?= htmlspecialchars($libro['titulo']) ?>"
                    required
                >
            </div>

            <!-- AUTOR -->
            <div class="form-grupo">
                <label for="autor">Autor *</label>
                <input
                    type="text"
                    id="autor"
                    name="autor"
                    value="<?= htmlspecialchars($libro['autor']) ?>"
                    required
                >
            </div>

            <!-- AÑO -->
            <div class="form-grupo">
                <label for="año_publicacion">Año de publicación *</label>
                <input
                    type="number"
                    id="año_publicacion"
                    name="año_publicacion"
                    min="1000"
                    max="<?= date('Y') ?>"
                    value="<?= htmlspecialchars($libro['año_publicacion']) ?>"
                    required
                >
            </div>

            <!-- GÉNERO -->
            <div class="form-grupo">
                <label for="genero">Género *</label>
                <select id="genero" name="genero" required>
                    <option value="">-- Selecciona un género --</option>
                    <?php foreach ($generos as $g): ?>
                        <!--
                            Comparamos el género actual del libro con cada opción.
                            Si coinciden añadimos 'selected' para que aparezca
                            preseleccionado al cargar el formulario.
                        -->
                        <option value="<?= $g ?>"
                            <?= ($libro['genero'] === $g) ? 'selected' : '' ?>>
                            <?= $g ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- PÁGINAS -->
            <div class="form-grupo">
                <label for="paginas">Número de páginas *</label>
                <input
                    type="number"
                    id="paginas"
                    name="paginas"
                    min="1"
                    value="<?= htmlspecialchars($libro['paginas']) ?>"
                    required
                >
            </div>

            <!-- CHECKBOX LEÍDO
                 'checked' se añade si $libro['leido'] es 1 (verdadero).
                 El == 1 (laxa) cubre tanto el int 1 como el string '1'
                 que puede devolver PDO según el contexto. -->
            <div class="form-grupo checkbox">
                <input
                    type="checkbox"
                    id="leido"
                    name="leido"
                    onchange="toggleValoracion(this.checked)"
                    <?= ($libro['leido'] == 1) ? 'checked' : '' ?>
                >
                <label for="leido">¿Ya lo has leído?</label>
            </div>

            <!-- VALORACIÓN
                 El div empieza visible si el libro ya estaba marcado como leído,
                 oculto si no. PHP decide el display inicial según $libro['leido'].
                 JS gestiona los cambios dinámicos posteriores. -->
            <div class="form-grupo" id="grupo-valoracion"
                 style="display: <?= ($libro['leido'] == 1) ? 'block' : 'none' ?>;">
                <label for="valoracion">Valoración</label>
                <select id="valoracion" name="valoracion">
                    <option value="">-- Sin valorar --</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <!--
                            == (laxa) porque $libro['valoracion'] puede ser
                            string '3' (desde BD vía PDO) o int 3 (desde POST).
                        -->
                        <option value="<?= $i ?>"
                            <?= ($libro['valoracion'] == $i) ? 'selected' : '' ?>>
                            <?= str_repeat('⭐', $i) ?> (<?= $i ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- BOTONES -->
            <div class="form-botones">
                <button type="submit" class="btn-submit">💾 Guardar cambios</button>
                <a href="index.php" class="btn-volver">✖ Cancelar</a>
            </div>

        </form>

    <?php endif; ?>

</div><!-- /form-card -->
</div><!-- /container -->

<script>
    // Misma función que en agregar.php: muestra/oculta valoración
    function toggleValoracion(marcado) {
        const grupo    = document.getElementById('grupo-valoracion');
        const select   = document.getElementById('valoracion');
        if (marcado) {
            grupo.style.display = 'block';
        } else {
            grupo.style.display = 'none';
            select.value = '';
        }
    }
</script>

</body>
</html>