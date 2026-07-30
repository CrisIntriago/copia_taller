<?php
function archivoTareas($usuario) {
    return "tareas_" . $usuario . ".csv";
}

function guardarTarea($usuario, $texto) {
    $archivo = archivoTareas($usuario);
    $id = 1;
    if (file_exists($archivo)) {
        $lineas = file($archivo);
        $id = count($lineas) + 1;
    }
    $linea = $id . "," . $texto . "," . "pendiente" . "\n";
    file_put_contents($archivo, $linea, FILE_APPEND);
}

function listarTareas($usuario) {
    $archivo = archivoTareas($usuario);
    $pendientes = [];
    $completadas = [];
    if (!file_exists($archivo)) return [$pendientes, $completadas];

    $lineas = file($archivo);
    foreach ($lineas as $linea) {
        $campos = explode(",", trim($linea));
        if (count($campos) < 3) continue;
        $tarea = [
            'id'     => $campos[0],
            'texto'  => $campos[1],
            'estado' => $campos[2]
        ];
        if ($tarea['estado'] === 'completada') {
            $completadas[] = $tarea;
        } else {
            $pendientes[] = $tarea;
        }
    }
    return [$pendientes, $completadas];
}

function completarTarea($usuario, $id) {
    $archivo = archivoTareas($usuario);
    if (!file_exists($archivo)) return;

    $lineas = file($archivo);
    $nuevas = [];
    foreach ($lineas as $linea) {
        $campos = explode(",", trim($linea));
        if (count($campos) < 3) continue;
        if ($campos[0] == $id) {
            $campos[2] = "completada";
        }
        $nuevas[] = implode(",", $campos);
    }
    file_put_contents($archivo, implode("\n", $nuevas) . "\n");
}

function eliminarTarea($usuario, $id) {
    $archivo = archivoTareas($usuario);
    if (!file_exists($archivo)) return;

    $lineas = file($archivo);
    $nuevas = [];
    foreach ($lineas as $linea) {
        $campos = explode(",", trim($linea));
        if (count($campos) < 3) continue;
        if ($campos[0] != $id) {
            $nuevas[] = implode(",", $campos);
        }
    }
    file_put_contents($archivo, implode("\n", $nuevas) . ($nuevas ? "\n" : ""));
}
?>
