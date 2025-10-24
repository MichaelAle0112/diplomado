<?php
include '../config/db.php';
include '../includes/header.php';
include '../includes/bitacora.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// CRUD para Usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['nombre'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['rol']]);
        registrarBitacora($pdo, $_SESSION['user_id'], 'INSERT', 'usuarios', $pdo->lastInsertId(), 'Usuario agregado');
    } elseif (isset($_POST['update_user'])) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?");
        $stmt->execute([$_POST['nombre'], $_POST['email'], $_POST['rol'], $_POST['id']]);
        registrarBitacora($pdo, $_SESSION['user_id'], 'UPDATE', 'usuarios', $_POST['id'], 'Usuario modificado');
    } elseif (isset($_POST['delete_user'])) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
        $stmt->execute([$_POST['id']]);
        registrarBitacora($pdo, $_SESSION['user_id'], 'DELETE', 'usuarios', $_POST['id'], 'Usuario eliminado');
    }
    // Similar para menu, reservas, pedidos (agrega bloques análogos)
}

// Obtener datos para listar
$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll();
$menu = $pdo->query("SELECT * FROM menu")->fetchAll();
$reservas = $pdo->query("SELECT r.*, u.nombre FROM reservas r JOIN usuarios u ON r.usuario_id = u.id")->fetchAll();
$pedidos = $pdo->query("SELECT p.*, u.nombre, m.nombre AS plato FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id JOIN menu m ON p.menu_id = m.id")->fetchAll();
$bitacora = $pdo->query("SELECT b.*, u.nombre FROM bitacora b LEFT JOIN usuarios u ON b.usuario_id = u.id ORDER BY b.fecha DESC LIMIT 50")->fetchAll();

?>
<h2>Panel de Administración</h2>
<!-- Secciones para CRUD: Usuarios, Menú, Reservas, Pedidos, Bitácora -->
<div class="admin-panel">
    <h3>Gestión de Usuarios</h3>
    <!-- Formulario para agregar -->
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="rol"><option value="cliente">Cliente</option><option value="admin">Admin</option></select>
        <button type="submit" name="add_user">Agregar Usuario</button>
    </form>
    <!-- Tabla para listar y acciones -->
    <table>
        <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr></thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo $u['nombre']; ?></td>
                    <td><?php echo $u['email']; ?></td>
                    <td><?php echo $u['rol']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <button type="submit" name="delete_user">Eliminar</button>
                        </form>
                        <!-- Agrega formularios inline para modificar -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Repite secciones similares para Menú, Reservas, Pedidos -->
    <h3>Bitácora de Acciones</h3>
    <table>
        <thead><tr><th>Usuario</th><th>Acción</th><th>Tabla</th><th>Detalles</th><th>Fecha</th></tr></thead>
        <tbody>
            <?php foreach ($bitacora as $b): ?>
                <tr>
                    <td><?php echo $b['nombre'] ?? 'Sistema'; ?></td>
                    <td><?php echo $b['accion']; ?></td>
                    <td><?php echo $b['tabla_afectada']; ?></td>
                    <td><?php echo $b['detalles']; ?></td>
                    <td><?php echo $b['fecha']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>