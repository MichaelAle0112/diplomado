<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabores del Valle - Restaurante</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
            <img src="/diplomado/images/logo.png" alt="Logo Sabores del Valle" width="50">
            <img src="/diplomado/images/restaurante.jpg" alt="Imagen del restaurante Sabores del Valle" class="footer-img">
                <h1>Sabores del Valle</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="menu.php">Menú</a></li>
                <li><a href="reservas.php">Reservas</a></li>
                <li><a href="pedidos.php">Pedidos</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                    <?php if ($_SESSION['rol'] == 'admin'): ?>
                        <li><a href="../admin/dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="login.php">Login / Registro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>

    <link rel="stylesheet" href="/diplomado/css/styles.css">