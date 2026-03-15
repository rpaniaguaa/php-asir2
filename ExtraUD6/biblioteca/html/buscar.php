<?php
// ============================================================
// buscar.php — Buscador de libros por título o autor
// Depende de: conexion.php, funciones.php, estilo.css
// Recibe: $_GET['q'] con el término de búsqueda
// ============================================================

require_once 'conexion.php';
require_once 'funciones.php';

// ============================================================
// 1. RECOGER EL TÉRMINO DE BÚSQUEDA
// Llega por GET porque los buscadores deben ser enlazables:
// buscar.php?q=Orwell → se puede compartir, guardar, volver atrás.
// trim() elimina espacios extremos accidentales.
// ?? '' evita Notice si la clave 'q' no existe en $_GET.
// ============================================================
$termino = trim($_GET['q'] ?? '');

// ============================================================
// 2. EJECUTAR LA BÚSQUEDA SOLO SI HAY TÉRMINO
// Si el campo llegó vacío no tiene sentido lanzar una query
// que devolvería TODOS los libros (LIKE '%%' coincide con todo).
// ============================================================
$libros    = [];   // Array vacío por defecto
$buscado   = false; // Flag para saber si el usuario ya buscó

if ($termino !== '') {
    $buscado = true;

    // Añadimos los comodines % AQUÍ en PHP, no dentro del SQL.
    // % antes y después permite búsqueda parcial:
    // "Gar" encontrará "Gabriel García Márquez"
    $parametro = '%' . $termino . '%';

    // --------------------------------------------------------
    // CONSULTA CON LIKE EN DOS COLUMNAS
    // Buscamos coincidencias en título Y también en autor.
    // El mismo marcador :busqueda se reutiliza dos veces,
    // pero en PDO con EMULATE_PREPARES=false hay que pasar
    // el valor dos veces si el marcador se repite.
    // Por eso usamos :busqueda1 y :busqueda2 (mismo valor,
    // distinto nombre) para evitar ese problema.
    // --------------------------------------------------------
    $sql = "SELECT * FROM libros
            WHERE titulo LIKE :busqueda1
               OR autor  LIKE :busqueda2
            ORDER BY titulo ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':busqueda1' => $parametro,
        ':busqueda2' => $parametro,
    ]);

    // fetchAll devuelve todas las filas encontradas como array
    $libros = $stmt->fetchAll();
}

// ============================================================
// 3. FUNCIÓN AUXILIAR: resaltarTermino()
// Rodea el término buscado con <mark> para que el navegador
// lo pinte en amarillo dentro de la celda de la tabla.
// htmlspecialchars se aplica PRIMERO (seguridad XSS) y
// LUEGO se inserta el <mark> de forma controlada.
// str_ireplace es insensible a mayúsculas/minúsculas (i = ignore case)
// ============================================================
function resaltarTermino($texto, $termino) {
    // Primero escapamos el texto para evitar XSS
    $textoSeguro    = htmlspecialchars($texto);
    // Escapamos también el término para usarlo en la búsqueda
    $terminoSeguro  = htmlspecialchars($termino);

    // str_ireplace reemplaza todas las ocurrencias sin importar mayúsculas
    // Envolvemos cada coincidencia en <mark>...</mark>
    return str_ireplace(
        $terminoSeguro,
        '<mark style="background:#e2b96f; color:#1a1a2e; border-radius:3px; padding:0 2px;">' . $terminoSeguro . '</mark>',
        $textoSeguro
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Buscar — Mi Biblioteca</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<!-- ============================================================
     CABECERA — igual que index.php para coherencia visual
     El buscador ya viene pre-rellenado con el término actual
     ============================================================ -->
<header>
    <h1>📚 Mi <span>Biblioteca</span></h1>

    <!--
        El value del input muestra el término buscado.
        htmlspecialchars evita que un término con < o " rompa el HTML.
        Al enviar de nuevo, se recarga buscar.php con el nuevo término.
    -->
    <form class="search-bar" action="buscar.php" method="GET">
        <input
            type="text"
            name="q"
            value="<?= htmlspecialchars($termino) ?>"
            placeholder="🔍 Buscar título o autor..."
            autocomplete="off"
            autofocus
        >
        <button class="btn-agregar" type="submit">Buscar</button>
    </form>
</header>

<div class="container">

    <!-- Enlace para volver al listado principal -->
    <a href="index.php" class="btn-volver" style="display:inline-block; margin-bottom:24px;">
        ← Volver al listado
    </a>

    <!-- ============================================================
         CABECERA DE RESULTADOS
         Mostramos cuántos resultados se encontraron o un mensaje
         si aún no se ha buscado nada.
         ============================================================ -->
    <?php if (!$buscado): ?>
        <!--
            Estado inicial: el usuario llegó a buscar.php sin término.
            Le invitamos a escribir algo.
        -->
        <div style="text-align:center; padding: 60px 20px; color:rgba(255,255,255,0.4);">
            <span style="font-size:3.5rem; display:block; margin-bottom:16px;">🔍</span>
            <p style="font-size:1.1rem;">Escribe un título o autor en el buscador para encontrar libros.</p>
        </div>

    <?php elseif (empty($libros)): ?>
        <!--
            Se buscó pero no se encontró nada.
            empty() devuelve true si el array no tiene elementos.
        -->
        <div class="sin-resultados">
            <span>📭</span>
            No se encontraron libros para
            <strong style="color:#e2b96f">"<?= htmlspecialchars($termino) ?>"</strong>
            <br><br>
            <a href="index.php" style="color:#e2b96f">← Ver todos los libros</a>
        </div>

    <?php else: ?>
        <!--
            Hay resultados: mostramos el contador y la tabla.
            count() devuelve el número de elementos del array $libros.
        -->
        <p style="color:rgba(255,255,255,0.5); margin-bottom:18px; font-size:0.9rem;">
            <?= count($libros) ?> resultado<?= count($libros) !== 1 ? 's' : '' ?>
            para <strong style="color:#e2b96f">"<?= htmlspecialchars($termino) ?>"</strong>
        </p>

        <!-- =====================================================
             TABLA DE RESULTADOS
             Misma estructura que index.php para coherencia.
             La diferencia: resaltarTermino() en título y autor.
             ===================================================== -->
        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Año</th>
                        <th>Género</th>
                        <th>Páginas</th>
                        <th>Estado</th>
                        <th>Valoración</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($libros as $libro): ?>
                    <tr>
                        <td><?= $libro['id'] ?></td>

                        <!--
                            resaltarTermino() envuelve la coincidencia en <mark>.
                            Usamos echo sin htmlspecialchars aquí porque
                            resaltarTermino() ya lo aplica internamente
                            y además añade HTML controlado (<mark>).
                        -->
                        <td><strong><?= resaltarTermino($libro['titulo'], $termino) ?></strong></td>

                        <td><?= resaltarTermino($libro['autor'], $termino) ?></td>

                        <td><?= $libro['año_publicacion'] ?></td>

                        <td><?= htmlspecialchars($libro['genero']) ?></td>

                        <td><?= $libro['paginas'] ?> pág.</td>

                        <!-- Badge de estado igual que en index.php -->
                        <td>
                            <?php if ($libro['leido'] == 1): ?>
                                <span class="badge badge-leido">✅ Leído</span>
                            <?php else: ?>
                                <span class="badge badge-pendiente">⏳ Pendiente</span>
                            <?php endif; ?>
                        </td>

                        <!-- Estrellas usando la función de funciones.php -->
                        <td><?= generarEstrellas($libro['valoracion']) ?></td>

                        <!-- Acciones: editar y eliminar igual que index.php -->
                        <td>
                            <a href="editar.php?id=<?= $libro['id'] ?>"
                               class="btn-editar">✏️ Editar</a>

                            <form method="POST" action="eliminar.php"
                                  style="display:inline;"
                                  onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars($libro['titulo'], ENT_QUOTES) ?>»?')">
                                <input type="hidden" name="id" value="<?= $libro['id'] ?>">
                                <button type="submit" class="btn-eliminar">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pie con total de resultados -->
        <p style="text-align:right; margin-top:14px; color:rgba(255,255,255,0.3); font-size:0.82rem;">
            <?= count($libros) ?> libro<?= count($libros) !== 1 ? 's' : '' ?> encontrado<?= count($libros) !== 1 ? 's' : '' ?>
        </p>

    <?php endif; ?>

</div><!-- /container -->

</body>
</html>