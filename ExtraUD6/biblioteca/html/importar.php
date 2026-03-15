<?php
// ============================================================
// importar.php — Importación masiva de libros desde CSV
// Depende de: conexion.php, funciones.php, estilo.css
// Flujo: mostrar formulario → subir CSV → validar cada línea
//        → INSERT de las válidas → resumen de resultados
// ============================================================

require_once 'conexion.php';
require_once 'funciones.php';

// Géneros válidos: deben coincidir exactamente con el ENUM de la BD
$generos_validos = ['Ficción', 'No Ficción', 'Ciencia', 'Historia', 'Tecnología', 'Arte', 'Otros'];

// Variables de resultado
$procesado   = false;  // True cuando ya se procesó un archivo
$insertados  = 0;      // Líneas insertadas con éxito
$fallidos    = [];     // Array de ['linea' => N, 'datos' => '...', 'errores' => [...]]

// ============================================================
// PROCESAMIENTO DEL ARCHIVO CSV
// Solo cuando llega un POST con el archivo adjunto.
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $procesado = true;

    // ----------------------------------------------------------
    // 1. VERIFICAR QUE EL ARCHIVO LLEGÓ SIN ERRORES
    // $_FILES['csv']['error'] === UPLOAD_ERR_OK (0) significa éxito.
    // Otros códigos: UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_NO_FILE, etc.
    // ----------------------------------------------------------
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        $error_subida = "Error al subir el archivo. Comprueba que seleccionaste un CSV.";
    } else {

        // ----------------------------------------------------------
        // 2. VALIDAR EXTENSIÓN DEL ARCHIVO
        // pathinfo extrae la extensión del nombre original del archivo.
        // strtolower para que .CSV y .csv sean equivalentes.
        // No usamos el tipo MIME ($_FILES['csv']['type']) porque
        // el navegador lo puede falsificar; la extensión es más fiable
        // para este caso de uso educativo.
        // ----------------------------------------------------------
        $nombre_original = $_FILES['csv']['name'];
        $extension       = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

        if ($extension !== 'csv') {
            $error_subida = "El archivo debe tener extensión .csv";
        } else {

            // ----------------------------------------------------------
            // 3. LEER EL ARCHIVO CSV LÍNEA A LÍNEA
            // fopen abre el archivo temporal que PHP creó al subir.
            // $_FILES['csv']['tmp_name'] es la ruta del archivo temporal.
            // fgetcsv lee una línea del CSV y la convierte en array
            // separando por comas. Devuelve false al llegar al final.
            // ----------------------------------------------------------
            $handle = fopen($_FILES['csv']['tmp_name'], 'r');

            if ($handle === false) {
                $error_subida = "No se pudo leer el archivo.";
            } else {

                // Saltamos la primera línea si es cabecera
                // Leemos la primera fila para detectar si es header
                $primera_fila = fgetcsv($handle, 1000, ',', '"', '\\');

                // Si la primera celda parece un título de columna (texto), la saltamos.
                // Si parece un dato real (empieza con letra y no es número), asumimos header.
                // Detectamos: si el primer campo es 'titulo' o 'Titulo' es cabecera.
                $es_cabecera = isset($primera_fila[0]) &&
                               strtolower(trim($primera_fila[0])) === 'titulo';

                // Si NO es cabecera, procesamos esa primera fila como dato
                if (!$es_cabecera && $primera_fila !== null) {
                    $fila_a_procesar = $primera_fila;
                    $lineas          = [1 => $fila_a_procesar]; // La guardamos para procesarla
                } else {
                    $lineas = [];
                }

                // Preparamos el INSERT fuera del bucle para reutilizarlo
                // (prepare una vez, execute muchas veces: más eficiente)
                $stmt_insert = $pdo->prepare(
                    "INSERT INTO libros
                        (titulo, autor, `año_publicacion`, genero, paginas, leido, valoracion)
                     VALUES
                        (:titulo, :autor, :anno_publicacion, :genero, :paginas, :leido, :valoracion)"
                );

                // Número de línea para el informe (empezamos en 2 si había cabecera)
                $num_linea = $es_cabecera ? 2 : 1;

                // Si había primera fila de datos, procesarla primero
                if (!$es_cabecera && !empty($lineas)) {
                    $fila = $lineas[1];
                    // (se procesará en el bucle de abajo al estar en $lineas)
                }

                // ----------------------------------------------------------
                // 4. BUCLE PRINCIPAL: procesar cada línea del CSV
                // Formato esperado de cada línea:
                // titulo, autor, año, genero, paginas, leido(0/1), valoracion(1-5 o vacío)
                // Ejemplo:
                // El Quijote, Miguel de Cervantes, 1605, Ficción, 863, 1, 5
                // ----------------------------------------------------------

                // Función que procesa una fila (array de campos CSV)
                $procesar_fila = function($fila, $num_linea) use (
                    $generos_validos, $stmt_insert, &$insertados, &$fallidos
                ) {
                    // Si fgetcsv devolvió false (línea vacía o error), ignorar
                    if ($fila === false || !is_array($fila)) {
                        return;
                    }

                    // Ignorar líneas completamente vacías
                    if (empty(array_filter($fila, fn($c) => trim($c) !== ''))) {
                        return;
                    }

                    // Verificar que tiene al menos 5 columnas obligatorias
                    if (count($fila) < 5) {
                        $fallidos[] = [
                            'linea'   => $num_linea,
                            'datos'   => implode(', ', $fila),
                            'errores' => ["La línea tiene menos de 5 columnas. Formato: titulo,autor,año,genero,paginas[,leido[,valoracion]]"]
                        ];
                        return;
                    }

                    // Extraer y limpiar cada campo
                    $datos = [
                        'titulo'          => trim($fila[0] ?? ''),
                        'autor'           => trim($fila[1] ?? ''),
                        'año_publicacion' => trim($fila[2] ?? ''),
                        'genero'          => trim($fila[3] ?? ''),
                        'paginas'         => trim($fila[4] ?? ''),
                        'leido'           => trim($fila[5] ?? '0'),
                        'valoracion'      => trim($fila[6] ?? ''),
                    ];

                    // --------------------------------------------------
                    // VALIDACIÓN con la función de funciones.php
                    // Reutilizamos validarLibro() exactamente igual que
                    // en agregar.php y editar.php.
                    // --------------------------------------------------
                    $errores_linea = [];

                    // Validación de género: no la hace validarLibro(), la añadimos aquí
                    // porque validarLibro no conoce el ENUM de la BD
                    if (!in_array($datos['genero'], $GLOBALS['generos_validos'] ?? [])) {
                        $errores_linea[] = "Género inválido: '{$datos['genero']}'";
                    }

                    // Validación de leido: debe ser 0 o 1
                    if (!in_array($datos['leido'], ['0', '1', 0, 1], true)) {
                        $datos['leido'] = '0'; // Valor por defecto si es inválido
                    }

                    // Validación de los campos comunes con validarLibro()
                    $resultado = validarLibro($datos);
                    if (!$resultado['valido']) {
                        $errores_linea = array_merge($errores_linea, $resultado['errores']);
                    }

                    if (!empty($errores_linea)) {
                        $fallidos[] = [
                            'linea'   => $num_linea,
                            'datos'   => implode(', ', $fila),
                            'errores' => $errores_linea
                        ];
                        return;
                    }

                    // --------------------------------------------------
                    // INSERT si la línea es válida
                    // --------------------------------------------------
                    $valoracion_final = ($datos['valoracion'] !== '')
                                        ? (int)$datos['valoracion']
                                        : null;

                    $stmt_insert->execute([
                        ':titulo'           => $datos['titulo'],
                        ':autor'            => $datos['autor'],
                        ':anno_publicacion' => (int)$datos['año_publicacion'],
                        ':genero'           => $datos['genero'],
                        ':paginas'          => (int)$datos['paginas'],
                        ':leido'            => (int)$datos['leido'],
                        ':valoracion'       => $valoracion_final,
                    ]);

                    $insertados++;
                };

                // Procesar primera fila si no era cabecera
                if (!$es_cabecera && $primera_fila !== null) {
                    $procesar_fila($primera_fila, $num_linea);
                    $num_linea++;
                }

                // Procesar el resto del archivo línea a línea
                while (($fila = fgetcsv($handle, 1000, ',', '"', '\\')) !== false) {
                    $procesar_fila($fila, $num_linea);
                    $num_linea++;
                }

                fclose($handle); // Siempre cerrar el archivo al terminar
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📥 Importar CSV — Mi Biblioteca</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        /* Zona de arrastre del archivo */
        .upload-area {
            border: 2px dashed rgba(226,185,111,0.4);
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 24px;
            transition: border-color 0.3s;
        }
        .upload-area:hover { border-color: #e2b96f; }
        .upload-area input[type="file"] {
            display: none; /* Ocultamos el input nativo feo */
        }
        .upload-area label {
            cursor: pointer;
            display: block;
        }
        .upload-area .icono-upload {
            font-size: 3rem;
            display: block;
            margin-bottom: 12px;
        }
        .upload-area .btn-elegir {
            display: inline-block;
            margin-top: 14px;
            padding: 10px 24px;
            background: rgba(226,185,111,0.15);
            border: 1px solid rgba(226,185,111,0.4);
            border-radius: 8px;
            color: #e2b96f;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
        }
        /* Tabla de resultados */
        .tabla-fallidos { font-size: 0.82rem; }
        .tabla-fallidos td { padding: 8px 12px; vertical-align: top; }
        .num-linea { color: #e2b96f; font-weight: bold; }
        .errores-lista { color: #f87171; margin: 0; padding-left: 16px; }
        /* Ejemplo CSV */
        .ejemplo-csv {
            background: #000;
            border-radius: 8px;
            padding: 16px 20px;
            color: #4ade80;
            font-size: 0.85rem;
            line-height: 1.8;
            margin-top: 16px;
            white-space: pre;
            overflow-x: auto;
        }
    </style>
</head>
<body>

<header>
    <h1>📚 Mi <span>Biblioteca</span></h1>
    <a href="index.php" class="btn-agregar">← Volver al listado</a>
</header>

<div class="container">
<div class="form-card" style="max-width:800px;">

    <h2>📥 Importar libros desde CSV</h2>

    <!-- ============================================================
         RESULTADO DEL PROCESAMIENTO
         Solo visible tras enviar el formulario ($procesado = true)
         ============================================================ -->
    <?php if ($procesado): ?>

        <?php if (isset($error_subida)): ?>
            <!-- Error general de subida -->
            <div class="alerta alerta-error">❌ <?= htmlspecialchars($error_subida) ?></div>

        <?php else: ?>
            <!-- Resumen de resultados -->
            <div class="alerta <?= $insertados > 0 ? 'alerta-exito' : 'alerta-error' ?>">
                ✅ <strong><?= $insertados ?></strong> libro<?= $insertados !== 1 ? 's' : '' ?> importado<?= $insertados !== 1 ? 's' : '' ?> correctamente.
                <?php if (!empty($fallidos)): ?>
                    &nbsp;|&nbsp; ❌ <strong><?= count($fallidos) ?></strong> línea<?= count($fallidos) !== 1 ? 's' : '' ?> con errores.
                <?php endif; ?>
            </div>

            <!-- Detalle de líneas con errores -->
            <?php if (!empty($fallidos)): ?>
                <h3 style="color:#f87171; margin:20px 0 12px;">Líneas con errores:</h3>
                <div class="tabla-wrapper">
                    <table class="tabla-fallidos">
                        <thead>
                            <tr>
                                <th>Línea</th>
                                <th>Datos recibidos</th>
                                <th>Errores</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fallidos as $f): ?>
                            <tr>
                                <td class="num-linea"><?= $f['linea'] ?></td>
                                <td style="color:#a0a0a0;"><?= htmlspecialchars($f['datos']) ?></td>
                                <td>
                                    <ul class="errores-lista">
                                        <?php foreach ($f['errores'] as $err): ?>
                                            <li><?= htmlspecialchars($err) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="form-botones" style="margin-top:24px;">
                <a href="index.php"    class="btn-submit">📚 Ver biblioteca</a>
                <a href="importar.php" class="btn-volver">📥 Importar otro CSV</a>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <!-- ============================================================
         FORMULARIO DE SUBIDA (siempre visible si no hubo éxito total)
         enctype="multipart/form-data" es OBLIGATORIO para subir archivos.
         Sin él, $_FILES estaría vacío.
         ============================================================ -->
    <?php if (!$procesado || isset($error_subida)): ?>

        <form method="POST" action="importar.php" enctype="multipart/form-data">

            <!-- Zona de clic para elegir archivo -->
            <div class="upload-area">
                <label for="csv">
                    <span class="icono-upload">📄</span>
                    <span style="color:rgba(255,255,255,0.6); font-size:0.95rem;">
                        Haz clic para seleccionar tu archivo CSV
                    </span>
                    <span class="btn-elegir">Elegir archivo CSV</span>
                    <!-- El input real está oculto; el label lo activa al hacer clic -->
                    <input type="file" id="csv" name="csv" accept=".csv"
                           onchange="mostrarNombre(this)">
                    <!-- Nombre del archivo seleccionado (lo actualiza JS) -->
                    <span id="nombre-archivo"
                          style="display:block;margin-top:10px;color:#e2b96f;font-size:0.85rem;">
                    </span>
                </label>
            </div>

            <div class="form-botones">
                <button type="submit" class="btn-submit">📥 Importar libros</button>
                <a href="index.php" class="btn-volver">✖ Cancelar</a>
            </div>
        </form>

        <!-- ============================================================
             INSTRUCCIONES Y EJEMPLO DE CSV
             ============================================================ -->
        <div style="margin-top:32px;">
            <h3 style="color:#e2b96f; margin-bottom:10px;">📋 Formato del CSV</h3>
            <p style="color:rgba(255,255,255,0.55); font-size:0.88rem; line-height:1.7;">
                El archivo debe tener las columnas en este orden, separadas por comas.<br>
                La primera fila puede ser una cabecera (se detecta automáticamente).<br>
                Los campos <strong>leido</strong> y <strong>valoracion</strong> son opcionales.
            </p>

            <div class="ejemplo-csv">titulo,autor,año_publicacion,genero,paginas,leido,valoracion
El Quijote,Miguel de Cervantes,1605,Ficción,863,1,5
Dune,Frank Herbert,1965,Ciencia,412,1,4
Cosmos,Carl Sagan,1980,Ciencia,365,0,
Harry Potter,J.K. Rowling,1997,Ficción,309,1,</div>

            <p style="color:rgba(255,255,255,0.4); font-size:0.8rem; margin-top:12px;">
                Géneros válidos: Ficción · No Ficción · Ciencia · Historia · Tecnología · Arte · Otros<br>
                Leído: 1 = sí, 0 = no (por defecto 0 si se omite)<br>
                Valoración: número del 1 al 5, dejar vacío si no se quiere valorar
            </p>
        </div>

    <?php endif; ?>

</div><!-- /form-card -->
</div><!-- /container -->

<script>
    // Muestra el nombre del archivo seleccionado debajo del botón
    function mostrarNombre(input) {
        const span = document.getElementById('nombre-archivo');
        if (input.files && input.files[0]) {
            span.textContent = '📄 ' + input.files[0].name;
        }
    }
</script>

</body>
</html>