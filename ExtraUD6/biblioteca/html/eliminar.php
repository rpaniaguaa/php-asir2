<?php
// ============================================================
// eliminar.php — Eliminar un libro de la biblioteca
// Depende de: conexion.php, estilo.css
// Flujo: recibe POST desde index.php o buscar.php
//        → valida ID → DELETE → mensaje resultado
// ============================================================

require_once 'conexion.php';
// No necesitamos funciones.php aquí: no usamos ninguna
// de las 4 funciones auxiliares en este archivo

// ============================================================
// SEGURIDAD: VERIFICAR MÉTODO POST
// Este archivo SOLO debe ejecutarse si llega una petición POST.
// Si alguien intenta acceder directamente por URL (GET):
//   - No habría ID válido
//   - No habría confirmación del usuario
// Lo bloqueamos redirigiendo a index.php.
// header() + exit() siempre juntos: sin exit, PHP seguiría
// ejecutando el código aunque el header ya esté enviado.
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// ============================================================
// VALIDAR EL ID RECIBIDO POR POST
// filter_var con FILTER_VALIDATE_INT devuelve:
//   - El entero si el valor es un entero válido
//   - false si no lo es (string, decimal, null...)
// Comparamos === false (estricto) para no confundir
// el valor 0 con false (trampa clásica de PHP).
// Además comprobamos que sea positivo (> 0) porque
// los IDs de AUTO_INCREMENT empiezan siempre en 1.
// ============================================================
$id_raw = $_POST['id'] ?? null;
$id     = filter_var($id_raw, FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    // ID inválido: guardamos el mensaje y mostramos error
    $error = "El ID del libro no es válido.";
} else {
    // ============================================================
    // GUARDAR EL TÍTULO ANTES DE ELIMINAR
    // Hacemos un SELECT previo para obtener el título del libro.
    // Esto nos permite mostrar el nombre en el mensaje de éxito
    // ("El libro X ha sido eliminado") en lugar de solo el ID.
    // También verifica que el libro existe antes de intentar borrarlo.
    // ============================================================
    $stmt = $pdo->prepare("SELECT titulo FROM libros WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $libro = $stmt->fetch();

    if ($libro === false) {
        // El libro no existe en la BD (ID inventado o ya eliminado)
        $error = "No se encontró ningún libro con ese ID.";

    } else {
        // Guardamos el título para el mensaje de confirmación
        $titulo_eliminado = $libro['titulo'];

        // ============================================================
        // DELETE CON PREPARED STATEMENT
        // El WHERE id = :id es IMPRESCINDIBLE.
        // Sin WHERE se borrarían TODOS los registros de la tabla.
        // El ID viaja como parámetro preparado, nunca concatenado.
        // ============================================================
        $stmt = $pdo->prepare("DELETE FROM libros WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // ============================================================
        // VERIFICAR CON rowCount()
        // Para DELETE, rowCount() SÍ es fiable (a diferencia de SELECT).
        // Devuelve el número de filas eliminadas.
        // Debería ser 1 si todo fue bien.
        // Si es 0 algo raro ocurrió (race condition: otro proceso
        // eliminó el mismo libro entre nuestro SELECT y nuestro DELETE).
        // ============================================================
        if ($stmt->rowCount() > 0) {
            $exito = true;
        } else {
            $error = "No se pudo eliminar el libro. Puede que ya haya sido eliminado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🗑️ Eliminar libro — Mi Biblioteca</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<header>
    <h1>📚 Mi <span>Biblioteca</span></h1>
    <a href="index.php" class="btn-agregar">← Volver al listado</a>
</header>

<div class="container">
<div class="form-card" style="max-width: 550px;">

    <!-- ============================================================
         CASO A: ELIMINACIÓN EXITOSA
         isset($exito) comprueba que la variable existe y es true.
         ============================================================ -->
    <?php if (isset($exito) && $exito): ?>

        <div style="text-align:center; padding: 20px 0;">
            <!-- Icono grande de confirmación -->
            <span style="font-size: 4rem; display:block; margin-bottom: 16px;">✅</span>

            <h2 style="color: #4ade80; margin-bottom: 12px;">Libro eliminado</h2>

            <div class="alerta alerta-exito">
                El libro <strong>"<?= htmlspecialchars($titulo_eliminado) ?>"</strong>
                ha sido eliminado correctamente de tu biblioteca.
            </div>

            <div class="form-botones" style="justify-content: center; margin-top: 24px;">
                <a href="index.php" class="btn-submit">📚 Volver al listado</a>
            </div>
        </div>

    <!-- ============================================================
         CASO B: ERROR (ID inválido, libro no encontrado, etc.)
         $error contiene el mensaje descriptivo del problema.
         ============================================================ -->
    <?php else: ?>

        <div style="text-align:center; padding: 20px 0;">
            <span style="font-size: 4rem; display:block; margin-bottom: 16px;">❌</span>

            <h2 style="color: #f87171; margin-bottom: 12px;">No se pudo eliminar</h2>

            <div class="alerta alerta-error">
                <?= htmlspecialchars($error ?? 'Error desconocido.') ?>
            </div>

            <div class="form-botones" style="justify-content: center; margin-top: 24px;">
                <a href="index.php" class="btn-submit">← Volver al listado</a>
            </div>
        </div>

    <?php endif; ?>

</div><!-- /form-card -->
</div><!-- /container -->

</body>
</html>