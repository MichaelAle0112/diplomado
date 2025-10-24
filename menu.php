<?php
include 'config/db.php';
include 'includes/header.php';

// Obtener todos los platos disponibles

$stmt = $pdo->query("SELECT * FROM menu WHERE disponible = TRUE ORDER BY categoria, nombre");
$platos = $stmt->fetchAll();

// Agrupar por categoría

$platos_por_categoria = [];
foreach ($platos as $plato) {
    $platos_por_categoria[$plato['categoria']][] = $plato;
}
// Nombres bonitos para categorías
$categorias_nombres = [
    'entrada' => '🍴 Entradas',
    'plato_principal' => '🍲 Platos Principales',
    'postre' => '🍰 Postres'
];
?>
<h2>Nuestro Menú</h2>

<?php foreach ($categorias_nombres as $categoria_key => $categoria_nombre): ?>
    <?php if (isset($platos_por_categoria[$categoria_key])): ?>
        <div class="categoria-section">
            <h3><?php echo $categoria_nombre; ?></h3>
            <div class="menu-grid">
                <?php foreach ($platos_por_categoria[$categoria_key] as $plato): ?>
                    <div class="menu-item">
                        <img src="<?php echo $plato['imagen']; ?>" 
                             alt="<?php echo htmlspecialchars($plato['nombre']); ?>" 
                             class="menu-img">
                        <div class="menu-info">
                            <h4><?php echo htmlspecialchars($plato['nombre']); ?></h4>
                            <p><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                            <div class="menu-price">
                                $<?php echo number_format($plato['precio'], 2); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<p class="text-center">
    <em>Para realizar un pedido, inicia sesión en <a href="pedidos.php">Pedidos</a>.</em>
</p>

<style>
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.menu-item {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.menu-item:hover {
    transform: translateY(-5px);
}

.menu-info h4 {
    color: #d2691e;
    margin: 1rem 0 0.5rem 0;
}

.menu-price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #228b22;
    margin-top: 1rem;
}

.categoria-section {
    margin: 3rem 0;
}

.categoria-section h3 {
    color: #8b4513;
    border-bottom: 2px solid #d2691e;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.text-center {
    text-align: center;
    margin-top: 2rem;
    font-style: italic;
    color: #666;
}
</style>

<?php include 'includes/footer.php'; ?>