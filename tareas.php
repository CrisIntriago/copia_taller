<?php
session_start();
require "tarea.php";

if (!isset($_SESSION['cedula'])) {
    header("Location: ingreso.php");
    exit;
}

$usuario = $_SESSION['cedula'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar' && !empty(trim($_POST['texto'] ?? ''))) {
        $texto = str_replace([",", "\n", "\r"], " ", trim($_POST['texto']));
        guardarTarea($usuario, $texto);
    } elseif ($accion === 'completar' && isset($_POST['id'])) {
        completarTarea($usuario, $_POST['id']);
    } elseif ($accion === 'eliminar' && isset($_POST['id'])) {
        eliminarTarea($usuario, $_POST['id']);
    }

    header("Location: tareas.php");
    exit;
}

[$pendientes, $completadas] = listarTareas($usuario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Tareas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="contenedor">
    <h1>Gestor de Tareas</h1>
    <p>Sesión: <?= htmlspecialchars($_SESSION['usuario'] ?? $usuario) ?> — <a href="logout.php">Cerrar sesión</a></p>

    <form method="POST" action="tareas.php">
        <input type="hidden" name="accion" value="agregar">
        <label>Nueva tarea:</label>
        <input type="text" name="texto" maxlength="200" required>
        <input type="submit" value="Agregar">
    </form>

    <h2>Pendientes</h2>
    <?php if (empty($pendientes)): ?>
        <p>No hay tareas pendientes.</p>
    <?php else: ?>
        <table>
            <tr><th>Tarea</th><th>Acciones</th></tr>
            <?php foreach ($pendientes as $tarea): ?>
            <tr>
                <td><?= htmlspecialchars($tarea['texto']) ?></td>
                <td>
                    <form method="POST" action="tareas.php" style="display:inline">
                        <input type="hidden" name="accion" value="completar">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($tarea['id']) ?>">
                        <input type="submit" value="Completar">
                    </form>
                    <form method="POST" action="tareas.php" style="display:inline">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($tarea['id']) ?>">
                        <input type="submit" value="Eliminar">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Completadas</h2>
    <?php if (empty($completadas)): ?>
        <p>No hay tareas completadas.</p>
    <?php else: ?>
        <table>
            <tr><th>Tarea</th><th>Acciones</th></tr>
            <?php foreach ($completadas as $tarea): ?>
            <tr>
                <td><?= htmlspecialchars($tarea['texto']) ?></td>
                <td>
                    <form method="POST" action="tareas.php" style="display:inline">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($tarea['id']) ?>">
                        <input type="submit" value="Eliminar">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
