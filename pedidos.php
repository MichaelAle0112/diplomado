<?php
include 'config/db.php';
include 'includes/header.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Obtener menú disponible
$stmt = $pdo->query("SELECT * FROM menu WHERE disponible = TRUE ORDER BY nombre");
$menu_items = $stmt->fetchAll();

// Procesar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = (int)$_POST['menu_id'];
    $cantidad = (int)$_POST['cantidad'];
    $usuario_id = $_SESSION['user_id'];

    // Validaciones
    if ($cantidad < 1 || $cantidad > 10) {
        $error = "La cantidad debe ser entre 1 y 10.";
    } else {
        // Obtener precio del menú
        $stmt = $pdo->prepare("SELECT precio FROM menu WHERE id = ?");
        $stmt->execute([$menu_id]);
        $menu_item = $stmt->fetch();
        
        if ($menu_item) {
            $total = $menu_item['precio'] * $cantidad;
            
            // Insertar pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, menu_id, cantidad, total) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$usuario_id, $menu_id, $cantidad, $total])) {
                $success = "¡Pedido realizado con éxito! Total: $" . number_format($total, 2);
            } else {
                $error = "Error al realizar el pedido. Por favor, intenta nuevamente.";
            }
        } else {
            $error = "Ítem de menú no válido.";
        }
    }
}
