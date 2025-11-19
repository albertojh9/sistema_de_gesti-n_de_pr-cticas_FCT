<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Vista: Dashboard Principal
 * 
 * @author Alberto Jiménez Hernández
 */
session_start();

// Verificar si está logueado
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: login.php');
    exit;
}

$nombre = $_SESSION['usuario_nombre'];
$rol = $_SESSION['usuario_rol'];
$mensaje = $_SESSION['mensaje'] ?? '';

// Limpiar mensaje
unset($_SESSION['mensaje']);

// Texto amigable para el rol
$rolTexto = [
    'ESTUDIANTE' => 'Estudiante',
    'TUTOR_EMPRESA' => 'Tutor de Empresa',
    'COORDINADOR' => 'Coordinador FCT'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema FCT</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="navbar">
            <div class="container navbar-content">
                <a href="dashboard.php" class="navbar-brand">Sistema FCT</a>
                <div class="navbar-user">
                    <span><?php echo htmlspecialchars($nombre); ?></span>
                    <a href="../controladores/AuthController.php?accion=logout" class="btn btn-logout">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </nav>
        
        <main class="dashboard-main">
            <div class="container">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
                
                <div class="welcome-card">
                    <h1>¡Bienvenido, <?php echo htmlspecialchars(explode(' ', $nombre)[0]); ?>!</h1>
                    <p>Has iniciado sesión correctamente en el Sistema de Gestión de Prácticas FCT.</p>
                    <span class="rol-badge"><?php echo $rolTexto[$rol] ?? $rol; ?></span>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
