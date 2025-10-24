<?php
include 'config/db.php';
session_start();
// Redirigir si ya está logueado



$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = "Credenciales inválidas. Por favor, intenta nuevamente.";
        }
    } 
    elseif (isset($_POST['register'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validaciones
        if (empty($nombre) || empty($email) || empty($password)) {
            $error = "Todos los campos son requeridos.";
        } elseif ($password !== $confirm_password) {
            $error = "Las contraseñas no coinciden.";
        } elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "El email ya está registrado.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
                
                if ($stmt->execute([$nombre, $email, $hashed_password])) {
                    $success = "Registro exitoso. Ahora puedes iniciar sesión.";
                } else {
                    $error = "Error en el registro. Por favor, intenta nuevamente.";
                }
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Iniciar Sesión</h2>
        <?php if ($error && isset($_POST['login'])): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form id="form-login" method="POST">
            <div class="form-group">
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="login-password">Contraseña:</label>
                <input type="password" id="login-password" name="password" required>
            </div>
            
            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
    </div>

    <div class="auth-form">
        <h2>Registrarse</h2>
        <?php if ($error && isset($_POST['register'])): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="register-nombre">Nombre:</label>
                <input type="text" id="register-nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="register-email">Email:</label>
                <input type="email" id="register-email" name="email" required>
            </div>

            <div class="form-group">
                <label for="register-password">Contraseña:</label>
                <input type="password" id="register-password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="register-confirm">Confirmar Contraseña:</label>
                <input type="password" id="register-confirm" name="confirm_password" required>
            </div>
            
            <button type="submit" name="register">Registrarse</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>