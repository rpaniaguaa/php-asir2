<?php
// ============================================================
// agregar.php — Formulario para añadir un nuevo libro
// Depende de: conexion.php, funciones.php, estilo.css
// Flujo: mostrar formulario → recibir POST → validar → INSERT
// ============================================================

require_once 'conexion.php';
require_once 'funciones.php';

// Lista fija de géneros: debe coincidir exactamente con
// el ENUM definido en la tabla de la base de datos
$generos = ['Ficción', 'No Ficción', 'Ciencia', 'Historia', 'Tecnología', 'Arte', 'Otros'];

// Variables de control del flujo de la página
$errores   = [];       // Array de mensajes de error de validación
$exito     = false;    // Flag que indica si el INSERT fue bien
$datos     = [];       // Guarda los valores del POST para rellenar el form si hay errores

// ============================================================
// PROCESAMIENTO DEL FORMULARIO
// Solo entramos en este bloque si el formulario fue enviado.
// $_SERVER['REQUEST_METHOD'] devuelve el método HTTP usado:
// 'GET' al cargar la página, 'POST' al enviar el formulario.
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ----------------------------------------------------------
    // 1. RECOGER Y LIMPIAR LOS DATOS DEL FORMULARIO
    // trim() elimina espacios al inicio y al final.
    // ?? '' evita Notice si la clave no existe en $_POST.
    // Los datos se guardan en $datos para poder repoblar
    // el formulario si la validación falla (mejor UX).
    // ----------------------------------------------------------
    $datos = [
        'titulo'           => trim($_POST['titulo']           ?? ''),
        'autor'            => trim($_POST['autor']            ?? ''),
        'año_publicacion'  => trim($_POST['año_publicacion']  ?? ''),
        'genero'           => trim($_POST['genero']           ?? ''),
        'paginas'          => trim($_POST['paginas']          ?? ''),

        // El checkbox "leido": si está marcado llega como 'on' en $_POST.
        // Si NO está marcado, la clave directamente NO existe en $_POST.
        // isset() comprueba existencia → devuelve true/false → (int) lo convierte a 1/0.
        'leido'            => isset($_POST['leido']) ? 1 : 0,

        // La valoración solo tiene sentido si el libro está marcado como leído.
        // Si 'leido' no existe (no marcado), forzamos valoración a null.
        // Si existe, recogemos el valor del select (puede ser '' si no eligió).
        'valoracion'       => isset($_POST['leido']) ? (trim($_POST['valoracion'] ?? '')) : null,
    ];

    // ----------------------------------------------------------
    // 2. VALIDAR CON LA FUNCIÓN DE funciones.php
    // validarLibro() recibe el array $datos y devuelve:
    // ['valido' => bool, 'errores' => array de strings]
    // ----------------------------------------------------------
    $resultado = validarLibro($datos);

    if (!$resultado['valido']) {
        // Hay errores: los guardamos para mostrarlos en el HTML
        $errores = $resultado['errores'];

    } else {
        // ----------------------------------------------------------
        // 3. INSERT CON PREPARED STATEMENT
        // Todos los valores van como parámetros nombrados (:nombre).
        // NUNCA se concatenan variables del usuario en el SQL.
        // La valoración puede ser null si el libro no está leído
        // o si no se especificó: PDO enviará NULL a MySQL correctamente.
        // ----------------------------------------------------------

        // Convertimos valoración vacía a null para que MySQL
        // almacene NULL en lugar de cadena vacía (que rompería el CHECK)
        $valoracion_final = ($datos['valoracion'] !== '' && $datos['valoracion'] !== null)
                            ? (int) $datos['valoracion']
                            : null;

        $sql = "INSERT INTO libros
                    (titulo, autor, `año_publicacion`, genero, paginas, leido, valoracion)
                VALUES
                    (:titulo, :autor, :anno_publicacion, :genero, :paginas, :leido, :valoracion)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo'            => $datos['titulo'],
            ':autor'             => $datos['autor'],
            ':anno_publicacion'  => (int) $datos['año_publicacion'],
            ':genero'            => $datos['genero'],
            ':paginas'           => (int) $datos['paginas'],
            ':leido'             => $datos['leido'],
            ':valoracion'        => $valoracion_final,
        ]);

        // Si llegamos aquí sin excepción, el INSERT fue exitoso.
        // Activamos el flag de éxito y limpiamos $datos para
        // que el formulario quede vacío (listo para otro libro).
        $exito = true;
        $datos = [];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>➕ Agregar libro — Mi Biblioteca</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<!-- Cabecera coherente con el resto de páginas -->
<header>
    <h1>📚 Mi <span>Biblioteca</span></h1>
    <a href="index.php" class="btn-agregar">← Volver al listado</a>
</header>

<div class="container">
<div class="form-card">

    <h2>➕ Agregar nuevo libro</h2>

    <!-- ============================================================
         MENSAJE DE ÉXITO
         Solo se muestra si el INSERT funcionó correctamente.
         Ofrecemos dos opciones: agregar otro o volver al listado.
         ============================================================ -->
    <?php if ($exito): ?>
        <div class="alerta alerta-exito">
            ✅ Libro añadido correctamente a tu biblioteca.
        </div>
        <div class="form-botones">
            <a href="index.php"   class="btn-submit">📚 Ver listado</a>
            <a href="agregar.php" class="btn-volver">➕ Agregar otro</a>
        </div>

    <?php else: ?>

        <!-- ============================================================
             LISTA DE ERRORES DE VALIDACIÓN
             Solo visible si $errores no está vacío.
             Cada elemento del array es un string con el mensaje.
             ============================================================ -->
        <?php if (!empty($errores)): ?>
            <ul class="lista-errores">
                <?php foreach ($errores as $error): ?>
                    <!-- htmlspecialchars por si el mensaje contuviera caracteres especiales -->
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- ============================================================
             FORMULARIO DE AGREGAR
             method="POST": los datos van en el cuerpo de la petición.
             action="agregar.php": el mismo archivo procesa el formulario
             (patrón PRG simplificado: el else final muestra el form).

             Los value de cada input usan $datos['campo'] ?? ''
             para repoblar el formulario si hubo errores de validación.
             ============================================================ -->
        <form method="POST" action="agregar.php">

            <!-- TÍTULO -->
            <div class="form-grupo">
                <label for="titulo">Título *</label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    placeholder="Ej: Cien años de soledad"
                    value="<?= htmlspecialchars($datos['titulo'] ?? '') ?>"
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
                    placeholder="Ej: Gabriel García Márquez"
                    value="<?= htmlspecialchars($datos['autor'] ?? '') ?>"
                    required
                >
            </div>

            <!-- AÑO DE PUBLICACIÓN -->
            <div class="form-grupo">
                <label for="año_publicacion">Año de publicación *</label>
                <input
                    type="number"
                    id="año_publicacion"
                    name="año_publicacion"
                    placeholder="Ej: 1967"
                    min="1000"
                    max="<?= date('Y') ?>"
                    value="<?= htmlspecialchars($datos['año_publicacion'] ?? '') ?>"
                    required
                >
            </div>

            <!-- GÉNERO (select con el ENUM de la BD) -->
            <div class="form-grupo">
                <label for="genero">Género *</label>
                <select id="genero" name="genero" required>
                    <option value="">-- Selecciona un género --</option>
                    <?php foreach ($generos as $g): ?>
                        <!--
                            Si el género de $datos coincide con esta opción,
                            añadimos el atributo 'selected' para que quede
                            seleccionado al repoblar el formulario.
                        -->
                        <option value="<?= $g ?>"
                            <?= (($datos['genero'] ?? '') === $g) ? 'selected' : '' ?>>
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
                    placeholder="Ej: 471"
                    min="1"
                    value="<?= htmlspecialchars($datos['paginas'] ?? '') ?>"
                    required
                >
            </div>

            <!-- CHECKBOX LEÍDO
                 El checkbox no envía valor si no está marcado.
                 El atributo 'checked' se añade condicionalmente si
                 al repoblar el formulario el libro estaba marcado.
                 onchange dispara JS para mostrar/ocultar la valoración. -->
            <div class="form-grupo checkbox">
                <input
                    type="checkbox"
                    id="leido"
                    name="leido"
                    onchange="toggleValoracion(this.checked)"
                    <?= (!empty($datos['leido'])) ? 'checked' : '' ?>
                >
                <label for="leido">¿Ya lo has leído?</label>
            </div>

            <!-- VALORACIÓN
                 Solo visible si el checkbox "leído" está marcado.
                 El div empieza oculto (display:none) y JavaScript
                 lo muestra/oculta al cambiar el checkbox.
                 Si el libro no está leído, no tiene sentido valorarlo. -->
            <div class="form-grupo" id="grupo-valoracion"
                 style="display: <?= (!empty($datos['leido'])) ? 'block' : 'none' ?>;">
                <label for="valoracion">Valoración</label>
                <select id="valoracion" name="valoracion">
                    <option value="">-- Sin valorar --</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"
                            <?= (($datos['valoracion'] ?? '') == $i) ? 'selected' : '' ?>>
                            <?= str_repeat('⭐', $i) ?> (<?= $i ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="form-botones">
                <button type="submit" class="btn-submit">💾 Guardar libro</button>
                <a href="index.php" class="btn-volver">✖ Cancelar</a>
            </div>

        </form>

    <?php endif; ?>

</div><!-- /form-card -->
</div><!-- /container -->

<!-- ============================================================
     JAVASCRIPT: mostrar/ocultar el campo de valoración
     según si el checkbox "leído" está marcado o no.
     Se ejecuta tanto al cambiar el checkbox (onchange)
     como al cargar la página si había datos previos.
     ============================================================ -->
<script>
    // Muestra u oculta el grupo de valoración según el estado del checkbox
    function toggleValoracion(marcado) {
        const grupoValoracion = document.getElementById('grupo-valoracion');
        const selectValoracion = document.getElementById('valoracion');

        if (marcado) {
            // Si se marca "leído" → mostramos el select de valoración
            grupoValoracion.style.display = 'block';
        } else {
            // Si se desmarca → ocultamos y reseteamos el select
            // para que no se envíe una valoración huérfana
            grupoValoracion.style.display = 'none';
            selectValoracion.value = '';
        }
    }
</script>

</body>
</html>