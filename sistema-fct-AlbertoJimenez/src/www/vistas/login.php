
<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Vista: Inicio de Sesión
 * 
 * @author Alberto Jiménez Hernández
 */
session_start();

// Obtener mensajes y errores de la sesión
$errores = $_SESSION['errores'] ?? [];
$mensaje = $_SESSION['mensaje'] ?? '';

// Limpiar mensajes
unset($_SESSION['errores']);
unset($_SESSION['mensaje']);

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['logueado']) && $_SESSION['logueado']) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema FCT</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h1 class="auth-title">Sistema FCT</h1>
                <p class="auth-subtitle">Gestión de Prácticas Formativas</p>
            </div>
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="../controladores/AuthController.php?accion=login" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="correo@ejemplo.com"
                        required
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="auth-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </div>
    </div>
</body>
</html>
