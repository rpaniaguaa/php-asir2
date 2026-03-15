<?php
// ============================================================
// index.php — Página principal de la Biblioteca Personal
// VERSIÓN CON BONUS:
//   - Paginación (10 libros por página) (+3 pts)
//   - Ordenamiento por columnas ASC/DESC   (+2 pts)
//   - Enlace a importar.php                (+5 pts)
// Depende de: conexion.php, funciones.php, estilo.css
// ============================================================

require_once 'conexion.php';
require_once 'funciones.php';

// ============================================================
// 1. ESTADÍSTICAS (igual que antes)
// ============================================================
$stats = obtenerEstadisticas($pdo);

// ============================================================
// 2. FILTROS (igual que antes)
// ============================================================
$filtro        = htmlspecialchars($_GET['filtro'] ?? 'todos');
$genero_filtro = htmlspecialchars($_GET['genero'] ?? '');
$generos       = ['Ficción', 'No Ficción', 'Ciencia', 'Historia', 'Tecnología', 'Arte', 'Otros'];

// ============================================================
// 3. ORDENAMIENTO
// El campo por el que ordenar llega en $_GET['orden'].
// La dirección (ASC/DESC) llega en $_GET['dir'].
// Usamos whitelist de columnas permitidas para evitar
// SQL Injection: NUNCA concatenamos $_GET directamente en SQL.
// ============================================================
$columnas_permitidas = ['titulo', 'autor', 'año_publicacion', 'paginas', 'valoracion'];

$orden_col = $_GET['orden'] ?? 'fecha_registro';
if (!in_array($orden_col, $columnas_permitidas)) {
    $orden_col = 'fecha_registro';
}

$orden_dir = strtoupper($_GET['dir'] ?? 'DESC');
if ($orden_dir !== 'ASC' && $orden_dir !== 'DESC') {
    $orden_dir = 'DESC';
}

// Dirección contraria: al hacer clic en una cabecera activa, alterna
$dir_contraria = ($orden_dir === 'ASC') ? 'DESC' : 'ASC';

// ============================================================
// 4. PAGINACIÓN
// 10 libros por página. Página actual en $_GET['pag'].
// OFFSET = (página - 1) × libros_por_página
// ============================================================
$por_pagina = 10;

$pag_raw = $_GET['pag'] ?? 1;
$pag = (filter_var($pag_raw, FILTER_VALIDATE_INT) && (int)$pag_raw >= 1)
       ? (int)$pag_raw : 1;

$offset = ($pag - 1) * $por_pagina;

// ============================================================
// 5. CONSTRUIR LA CONSULTA SQL
// Primero COUNT(*) para saber el total (necesario para paginación).
// Luego SELECT con ORDER BY + LIMIT + OFFSET.
// ============================================================
$where  = "WHERE 1=1";
$params = [];

if ($filtro === 'leidos') {
    $where .= " AND leido = 1";
} elseif ($filtro === 'pendientes') {
    $where .= " AND leido = 0";
}

if ($genero_filtro !== '') {
    $where .= " AND genero = :genero";
    $params[':genero'] = $genero_filtro;
}

// Contar total con los mismos filtros (sin LIMIT)
$stmt_total = $pdo->prepare("SELECT COUNT(*) AS total FROM libros $where");
$stmt_total->execute($params);
$total_filas = (int) $stmt_total->fetch()['total'];

// Total de páginas: ceil redondea hacia arriba
$total_paginas = (int) ceil($total_filas / $por_pagina);

// Corregir página si supera el máximo
if ($pag > $total_paginas && $total_paginas > 0) {
    $pag    = $total_paginas;
    $offset = ($pag - 1) * $por_pagina;
}

// Consulta real: ORDER BY validado + LIMIT + OFFSET casteados a int
$sql = "SELECT * FROM libros $where
        ORDER BY $orden_col $orden_dir
        LIMIT " . (int)$por_pagina . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$libros = $stmt->fetchAll();

// ============================================================
// 6. FUNCIONES HELPER PARA URLS
// Construyen hrefs preservando todos los parámetros GET activos.
// http_build_query convierte un array en cadena de query string.
// ============================================================

// URL para ordenar por una columna (vuelve a página 1)
function urlOrden($col, $dir, $filtro, $genero, $pag) {
    $p = ['orden' => $col, 'dir' => $dir, 'pag' => 1];
    if ($filtro !== 'todos') $p['filtro'] = $filtro;
    if ($genero !== '')      $p['genero'] = $genero;
    return 'index.php?' . http_build_query($p);
}

// URL para ir a una página concreta manteniendo filtros y orden
function urlPagina($pag_destino, $filtro, $genero, $orden_col, $orden_dir) {
    $p = ['pag' => $pag_destino, 'orden' => $orden_col, 'dir' => $orden_dir];
    if ($filtro !== 'todos') $p['filtro'] = $filtro;
    if ($genero !== '')      $p['genero'] = $genero;
    return 'index.php?' . http_build_query($p);
}

// Icono de ordenamiento: ▲ ▼ en columna activa, ⇅ en las demás
function iconoOrden($col, $orden_col, $orden_dir) {
    if ($col !== $orden_col) return '<span style="opacity:0.3">⇅</span>';
    return $orden_dir === 'ASC' ? ' ▲' : ' ▼';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 Mi Biblioteca Personal</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        /* Cabeceras de tabla ordenables */
        thead th a {
            color: #e2b96f;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        thead th a:hover { color: #fff; }

        /* Paginación */
        .paginacion {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .paginacion a,
        .paginacion span {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.05);
            color: #ccc;
            transition: all 0.2s;
        }
        .paginacion a:hover {
            background: #e2b96f;
            color: #1a1a2e;
            border-color: #e2b96f;
        }
        .paginacion span.activa {
            background: #e2b96f;
            color: #1a1a2e;
            border-color: #e2b96f;
        }
        .paginacion span.deshabilitado {
            opacity: 0.3;
            cursor: default;
        }
    </style>
</head>
<body>

<!-- CABECERA -->
<header>
    <h1>📚 Mi <span>Biblioteca</span></h1>
    <form class="search-bar" action="buscar.php" method="GET">
        <input type="text" name="q" placeholder="🔍 Buscar título o autor..." autocomplete="off">
        <button class="btn-agregar" type="submit">Buscar</button>
    </form>
</header>

<div class="container">

    <!-- ====================================================
         CARDS DE ESTADÍSTICAS
         ==================================================== -->
    <div class="stats-grid">
        <div class="stat-card card-total">
            <span class="icon">📖</span>
            <span class="valor"><?= $stats['total_libros'] ?></span>
            <span class="etiqueta">Total de libros</span>
        </div>
        <div class="stat-card card-leidos">
            <span class="icon">✅</span>
            <span class="valor"><?= $stats['libros_leidos'] ?></span>
            <span class="etiqueta">Libros leídos</span>
        </div>
        <div class="stat-card card-pendientes">
            <span class="icon">⏳</span>
            <span class="valor"><?= $stats['libros_pendientes'] ?></span>
            <span class="etiqueta">Pendientes</span>
        </div>
        <div class="stat-card card-promedio">
            <span class="icon">⭐</span>
            <span class="valor"><?= $stats['promedio_valoracion'] ?></span>
            <span class="etiqueta">Valoración media</span>
        </div>
    </div>

    <!-- ====================================================
         BARRA DE ACCIONES: filtros + botones
         ==================================================== -->
    <div class="actions-bar">
        <div class="filtros">

            <a href="<?= urlPagina(1, 'todos', '', $orden_col, $orden_dir) ?>"
               class="<?= ($filtro === 'todos' && $genero_filtro === '') ? 'activo' : '' ?>">
                📚 Todos
            </a>
            <a href="<?= urlPagina(1, 'leidos', $genero_filtro, $orden_col, $orden_dir) ?>"
               class="<?= $filtro === 'leidos' ? 'activo' : '' ?>">
                ✅ Leídos
            </a>
            <a href="<?= urlPagina(1, 'pendientes', $genero_filtro, $orden_col, $orden_dir) ?>"
               class="<?= $filtro === 'pendientes' ? 'activo' : '' ?>">
                ⏳ Pendientes
            </a>

            <!-- Select género: preserva filtro y orden -->
            <form method="GET" action="index.php" style="display:inline;">
                <?php if ($filtro !== 'todos'): ?>
                    <input type="hidden" name="filtro" value="<?= $filtro ?>">
                <?php endif; ?>
                <input type="hidden" name="orden" value="<?= $orden_col ?>">
                <input type="hidden" name="dir"   value="<?= $orden_dir ?>">
                <input type="hidden" name="pag"   value="1">
                <select name="genero" onchange="this.form.submit()">
                    <option value="">🏷️ Por género...</option>
                    <?php foreach ($generos as $g): ?>
                        <option value="<?= $g ?>" <?= $genero_filtro === $g ? 'selected' : '' ?>>
                            <?= $g ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <span style="color:rgba(255,255,255,0.4);font-size:0.82rem;padding:8px 0;">
                🏆 Favorito: <strong style="color:#e2b96f"><?= $stats['genero_favorito'] ?></strong>
            </span>

        </div>

        <!-- Botones agregar e importar CSV -->
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="importar.php" class="btn-volver" style="padding:11px 20px;">
                📥 Importar CSV
            </a>
            <a href="agregar.php" class="btn-agregar">➕ Agregar libro</a>
        </div>
    </div>

    <!-- ====================================================
         TABLA CON CABECERAS ORDENABLES
         ==================================================== -->
    <div class="tabla-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <a href="<?= urlOrden('titulo',
                            $orden_col === 'titulo' ? $dir_contraria : 'ASC',
                            $filtro, $genero_filtro, $pag) ?>">
                            Título <?= iconoOrden('titulo', $orden_col, $orden_dir) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= urlOrden('autor',
                            $orden_col === 'autor' ? $dir_contraria : 'ASC',
                            $filtro, $genero_filtro, $pag) ?>">
                            Autor <?= iconoOrden('autor', $orden_col, $orden_dir) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= urlOrden('año_publicacion',
                            $orden_col === 'año_publicacion' ? $dir_contraria : 'DESC',
                            $filtro, $genero_filtro, $pag) ?>">
                            Año <?= iconoOrden('año_publicacion', $orden_col, $orden_dir) ?>
                        </a>
                    </th>
                    <th>Género</th>
                    <th>
                        <a href="<?= urlOrden('paginas',
                            $orden_col === 'paginas' ? $dir_contraria : 'ASC',
                            $filtro, $genero_filtro, $pag) ?>">
                            Páginas <?= iconoOrden('paginas', $orden_col, $orden_dir) ?>
                        </a>
                    </th>
                    <th>Estado</th>
                    <th>
                        <a href="<?= urlOrden('valoracion',
                            $orden_col === 'valoracion' ? $dir_contraria : 'DESC',
                            $filtro, $genero_filtro, $pag) ?>">
                            Valoración <?= iconoOrden('valoracion', $orden_col, $orden_dir) ?>
                        </a>
                    </th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

            <?php if (empty($libros)): ?>
                <tr>
                    <td colspan="9">
                        <div class="sin-resultados">
                            <span>📭</span>
                            No se encontraron libros con este filtro.
                            <br><br>
                            <a href="index.php" style="color:#e2b96f">Ver todos los libros</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($libros as $libro): ?>
                <tr>
                    <td><?= $libro['id'] ?></td>
                    <td><strong><?= htmlspecialchars($libro['titulo']) ?></strong></td>
                    <td><?= htmlspecialchars($libro['autor']) ?></td>
                    <td><?= $libro['año_publicacion'] ?></td>
                    <td><?= htmlspecialchars($libro['genero']) ?></td>
                    <td><?= $libro['paginas'] ?> pág.</td>
                    <td>
                        <?php if ($libro['leido'] == 1): ?>
                            <span class="badge badge-leido">✅ Leído</span>
                        <?php else: ?>
                            <span class="badge badge-pendiente">⏳ Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td><?= generarEstrellas($libro['valoracion']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $libro['id'] ?>" class="btn-editar">✏️ Editar</a>
                        <form method="POST" action="eliminar.php" style="display:inline;"
                              onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars($libro['titulo'], ENT_QUOTES) ?>»?')">
                            <input type="hidden" name="id" value="<?= $libro['id'] ?>">
                            <button type="submit" class="btn-eliminar">🗑️ Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

    <!-- Pie: contador -->
    <p style="text-align:right;margin-top:14px;color:rgba(255,255,255,0.3);font-size:0.82rem;">
        Mostrando <?= count($libros) ?> de <?= $total_filas ?>
        libro<?= $total_filas !== 1 ? 's' : '' ?>
        — Página <?= $pag ?> de <?= max(1, $total_paginas) ?>
    </p>

    <!-- ====================================================
         PAGINACIÓN
         Solo aparece si hay más de una página.
         ==================================================== -->
    <?php if ($total_paginas > 1): ?>
    <div class="paginacion">

        <!-- Anterior -->
        <?php if ($pag > 1): ?>
            <a href="<?= urlPagina($pag - 1, $filtro, $genero_filtro, $orden_col, $orden_dir) ?>">
                ← Anterior
            </a>
        <?php else: ?>
            <span class="deshabilitado">← Anterior</span>
        <?php endif; ?>

        <?php
        // Rango de páginas visibles alrededor de la actual
        $rango_inicio = max(1, $pag - 2);
        $rango_fin    = min($total_paginas, $pag + 2);
        ?>

        <!-- Primera página + puntos suspensivos si el rango no empieza en 1 -->
        <?php if ($rango_inicio > 1): ?>
            <a href="<?= urlPagina(1, $filtro, $genero_filtro, $orden_col, $orden_dir) ?>">1</a>
            <?php if ($rango_inicio > 2): ?>
                <span style="border:none;background:none;opacity:0.4">…</span>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Páginas del rango central -->
        <?php for ($p = $rango_inicio; $p <= $rango_fin; $p++): ?>
            <?php if ($p === $pag): ?>
                <span class="activa"><?= $p ?></span>
            <?php else: ?>
                <a href="<?= urlPagina($p, $filtro, $genero_filtro, $orden_col, $orden_dir) ?>">
                    <?= $p ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- Última página + puntos si el rango no llega al final -->
        <?php if ($rango_fin < $total_paginas): ?>
            <?php if ($rango_fin < $total_paginas - 1): ?>
                <span style="border:none;background:none;opacity:0.4">…</span>
            <?php endif; ?>
            <a href="<?= urlPagina($total_paginas, $filtro, $genero_filtro, $orden_col, $orden_dir) ?>">
                <?= $total_paginas ?>
            </a>
        <?php endif; ?>

        <!-- Siguiente -->
        <?php if ($pag < $total_paginas): ?>
            <a href="<?= urlPagina($pag + 1, $filtro, $genero_filtro, $orden_col, $orden_dir) ?>">
                Siguiente →
            </a>
        <?php else: ?>
            <span class="deshabilitado">Siguiente →</span>
        <?php endif; ?>

    </div>
    <?php endif; ?>

</div><!-- /container -->
</body>
</html>