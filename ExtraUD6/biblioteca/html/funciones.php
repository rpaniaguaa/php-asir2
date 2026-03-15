<?php

// ============================================================
// FUNCIÓN 1: obtenerEstadisticas($pdo)
// Consulta la BD y devuelve un array con datos globales
// de la biblioteca para mostrar en las cards de index.php
// ============================================================
function obtenerEstadisticas($pdo) {

    // --- Total de libros ---
    // COUNT(*) cuenta todas las filas sin filtro
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM libros");
    $total = $stmt->fetch()['total'];

    // --- Libros leídos ---
    // El campo 'leido' es BOOLEAN: en MySQL 1 = true, 0 = false
    $stmt = $pdo->query("SELECT COUNT(*) AS leidos FROM libros WHERE leido = 1");
    $leidos = $stmt->fetch()['leidos'];

    // --- Libros pendientes ---
    // No hacemos otra query: es simplemente total - leídos
    $pendientes = $total - $leidos;

    // --- Promedio de valoración ---
    // AVG() calcula la media. La condición "valoracion IS NOT NULL"
    // excluye los libros sin valorar para no distorsionar la media.
    // ROUND(..., 1) redondea a 1 decimal (ej: 4.5)
    $stmt = $pdo->query("SELECT ROUND(AVG(valoracion), 1) AS promedio
                         FROM libros
                         WHERE valoracion IS NOT NULL");
    $promedio = $stmt->fetch()['promedio'];

    // Si no hay ningún libro valorado, AVG devuelve NULL
    // Le asignamos un string descriptivo para mostrarlo en la card
    if ($promedio === null) {
        $promedio = 'Sin valoraciones';
    }

    // --- Género favorito ---
    // GROUP BY agrupa los libros por género.
    // COUNT(*) cuenta cuántos hay en cada grupo.
    // ORDER BY ... DESC pone el mayor primero.
    // LIMIT 1 se queda solo con el género más frecuente.
    $stmt = $pdo->query("SELECT genero, COUNT(*) AS cantidad
                         FROM libros
                         GROUP BY genero
                         ORDER BY cantidad DESC
                         LIMIT 1");
    $fila = $stmt->fetch();

    // Si la tabla está vacía fetch() devuelve false → ponemos guion
    $genero_favorito = $fila ? $fila['genero'] : '-';

    // --- Devolvemos todo en un array asociativo ---
    // Cada clave corresponde a lo que usarán los otros archivos
    return [
        'total_libros'        => $total,
        'libros_leidos'       => $leidos,
        'libros_pendientes'   => $pendientes,
        'promedio_valoracion' => $promedio,
        'genero_favorito'     => $genero_favorito
    ];
}


// ============================================================
// FUNCIÓN 2: validarLibro($datos)
// Recibe el array $_POST con los campos del formulario y
// comprueba que todos los valores sean correctos.
// Devuelve ['valido' => bool, 'errores' => array]
// Usada en: agregar.php y editar.php antes de INSERT/UPDATE
// ============================================================
function validarLibro($datos) {

    // Array donde iremos acumulando los mensajes de error
    $errores = [];

    // --- Validar título ---
    // trim() elimina espacios extremos antes de medir la longitud
    // empty() detecta cadena vacía; strlen() comprueba mínimo 3 chars
    $titulo = trim($datos['titulo'] ?? '');
    if (empty($titulo)) {
        $errores[] = "El título es obligatorio.";
    } elseif (strlen($titulo) < 3) {
        $errores[] = "El título debe tener al menos 3 caracteres.";
    }

    // --- Validar autor ---
    // Solo comprobamos que no esté vacío
    $autor = trim($datos['autor'] ?? '');
    if (empty($autor)) {
        $errores[] = "El autor es obligatorio.";
    }

    // --- Validar año de publicación ---
    // filter_var con FILTER_VALIDATE_INT comprueba que sea entero estricto
    // (intval('abc') devolvería 0 sin avisar, por eso no lo usamos)
    // date('Y') devuelve el año actual como string, lo casteamos a int
    $año = $datos['año_publicacion'] ?? '';
    $año_actual = (int) date('Y');
    if (filter_var($año, FILTER_VALIDATE_INT) === false) {
        $errores[] = "El año debe ser un número entero.";
    } elseif ((int)$año < 1000 || (int)$año > $año_actual) {
        $errores[] = "El año debe estar entre 1000 y $año_actual.";
    }

    // --- Validar páginas ---
    // Debe ser entero y mayor que 0
    $paginas = $datos['paginas'] ?? '';
    if (filter_var($paginas, FILTER_VALIDATE_INT) === false || (int)$paginas <= 0) {
        $errores[] = "El número de páginas debe ser un número positivo.";
    }

    // --- Validar valoración (solo si se envió) ---
    // La valoración es opcional: solo existe si el libro está marcado como leído.
    // isset() comprueba que la clave exista Y no sea null.
    // Si existe, debe ser un entero entre 1 y 5.
    if (isset($datos['valoracion']) && $datos['valoracion'] !== '') {
        $val = $datos['valoracion'];
        if (filter_var($val, FILTER_VALIDATE_INT) === false
            || (int)$val < 1
            || (int)$val > 5) {
            $errores[] = "La valoración debe ser un número entre 1 y 5.";
        }
    }

    // --- Resultado ---
    // Si el array $errores está vacío, todo es válido
    return [
        'valido'  => empty($errores),
        'errores' => $errores
    ];
}


// ============================================================
// FUNCIÓN 3: formatearFecha($fecha)
// Convierte el formato de MySQL "2024-01-15 10:30:00"
// al formato legible en español "15 de Enero de 2024"
// Usada en: index.php y buscar.php al mostrar fecha_registro
// ============================================================
function formatearFecha($fecha) {

    // Array de traducción mes numérico → nombre en español
    // El índice 0 queda vacío para que los meses vayan de 1 a 12
    $meses = [
        '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    // strtotime() convierte el string de fecha a timestamp Unix (número entero)
    // Con ese timestamp podemos usar date() para extraer partes
    $timestamp = strtotime($fecha);

    // Extraemos día, mes (número) y año por separado
    $dia  = date('j', $timestamp);       // 'j' = día sin cero inicial (1-31)
    $mes  = (int) date('n', $timestamp); // 'n' = mes sin cero inicial (1-12)
    $año  = date('Y', $timestamp);       // 'Y' = año con 4 dígitos

    // Componemos el string final usando el array de meses
    return "$dia de {$meses[$mes]} de $año";
}


// ============================================================
// FUNCIÓN 4: generarEstrellas($valoracion)
// Convierte un número del 1 al 5 en un string de emojis
// de estrellas llenas ⭐ y vacías ☆
// Si la valoración es NULL devuelve "Sin valorar"
// Usada en: index.php y buscar.php al mostrar la valoración
// ============================================================
function generarEstrellas($valoracion) {

    // Comprobación de NULL: libros sin valorar (El Principito, Clean Code)
    if ($valoracion === null) {
        return "Sin valorar";
    }

    // Construimos el string de estrellas con un bucle del 1 al 5
    // Si la posición i es <= a la valoración → estrella llena ⭐
    // Si no → estrella vacía ☆
    $estrellas = "";
    for ($i = 1; $i <= 5; $i++) {
        $estrellas .= ($i <= $valoracion) ? "⭐" : "☆";
    }

    return $estrellas;
}