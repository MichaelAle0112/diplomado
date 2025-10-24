<?php
function registrarBitacora($pdo, $usuario_id, $accion, $tabla, $registro_id = null, $detalles = '') {
    $stmt = $pdo->prepare("INSERT INTO bitacora (usuario_id, accion, tabla_afectada, registro_id, detalles) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $accion, $tabla, $registro_id, $detalles]);
}
?>