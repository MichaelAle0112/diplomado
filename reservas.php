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

// Procesar formulario de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $num_personas = (int)$_POST['num_personas'];
    $usuario_id = $_SESSION['user_id'];
    
    // Validaciones
    $fecha_actual = date('Y-m-d');
    $hora_actual = date('H:i');

    if ($fecha < $fecha_actual) {
        $error = "No puedes reservar para una fecha pasada.";
    } elseif ($fecha === $fecha_actual && $hora < $hora_actual) {
        $error = "No puedes reservar para una hora pasada.";
    } elseif ($num_personas < 1 || $num_personas > 20) {
        $error = "El número de personas debe ser entre 1 y 20.";
    } else {
        // Verificar disponibilidad (simplificado)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE fecha = ? AND hora = ? AND estado = 'confirmada'");
        $stmt->execute([$fecha, $hora]);
        $reservas_existentes = $stmt->fetchColumn();
        
        if ($reservas_existentes >= 5) { // Máximo 5 reservas por hora
            $error = "Lo sentimos, no hay mesas disponibles para esa hora.";
        } else {
            // Crear la reserva
            $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, fecha, hora, num_personas) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$usuario_id, $fecha, $hora, $num_personas])) {
                $success = "¡Reserva realizada con éxito! Te esperamos el $fecha a las $hora.";
            } else {
                $error = "Error al realizar la reserva. Por favor, intenta nuevamente.";
            }
        }
    }
}

// Obtener reservas del usuario
$stmt = $pdo->prepare("SELECT * FROM reservas WHERE usuario_id = ? ORDER BY fecha DESC, hora DESC");
$stmt->execute([$_SESSION['user_id']]);
$reservas = $stmt->fetchAll();
?>

<h2>Realizar Reserva</h2>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form id="form-reserva" method="POST">
    <div class="form-group">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required 
               min="<?php echo date('Y-m-d'); ?>">
    </div>

    <div class="form-group">
        <label for="hora">Hora:</label>
        <select id="hora" name="hora" required>
            <option value="">Selecciona una hora</option>
            <?php for ($h = 12; $h <= 22; $h++): ?>
                <?php for ($m = 0; $m < 60; $m += 30): ?>
                    <?php $hora_val = sprintf('%02d:%02d', $h, $m); ?>
                    <option value="<?php echo $hora_val; ?>">
                        <?php echo $hora_val; ?>
                    </option>
                <?php endfor; ?>
            <?php endfor; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="num_personas">Número de Personas:</label>
        <input type="number" id="num_personas" name="num_personas" 
               min="1" max="20" required>
    </div>
    
    <button type="submit">Reservar Mesa</button>
</form>

<?php if (!empty($reservas)): ?>
    <div class="mis-reservas">
        <h3>Mis Reservas</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Personas</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($reserva['fecha'])); ?></td>
                        <td><?php echo $reserva['hora']; ?></td>
                        <td><?php echo $reserva['num_personas']; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $reserva['estado']; ?>">
                                <?php echo ucfirst($reserva['estado']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>