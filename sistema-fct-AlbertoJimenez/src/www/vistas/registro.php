<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Vista: Registro de Usuario (Sin JavaScript - Sistema de 2 pasos)
 * 
 * @author Alberto Jiménez Hernández
 */
session_start();

require_once __DIR__ . '/../modelos/Usuario.php';

// Obtener errores y datos previos
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_form'] ?? [];
$empresas = (new Usuario())->obtenerEmpresas();

// Determinar el paso actual
$paso = isset($_GET['paso']) ? (int)$_GET['paso'] : 1;
$rolSeleccionado = $_GET['rol'] ?? ($datos['rol'] ?? '');

// Limpiar sesión de errores
unset($_SESSION['errores']);
unset($_SESSION['datos_form']);

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
    <title>Registro - Sistema FCT</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card registro">
            <div class="auth-header">
                <div class="auth-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <h1 class="auth-title">Crear Cuenta</h1>
                <p class="auth-subtitle">Regístrate en el Sistema FCT</p>
            </div>
            
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($paso == 1): ?>
            <!-- PASO 1: Seleccionar rol y datos básicos -->
            <div class="pasos-info">Paso 1 de 2: Datos básicos</div>
            
            <form action="registro.php?paso=2" method="GET">
                <div class="form-group">
                    <label for="rol" class="form-label">Tipo de usuario *</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="">Selecciona tu rol</option>
                        <option value="ESTUDIANTE" <?php echo $rolSeleccionado === 'ESTUDIANTE' ? 'selected' : ''; ?>>Estudiante</option>
                        <option value="TUTOR_EMPRESA" <?php echo $rolSeleccionado === 'TUTOR_EMPRESA' ? 'selected' : ''; ?>>Tutor de Empresa</option>
                        <option value="COORDINADOR" <?php echo $rolSeleccionado === 'COORDINADOR' ? 'selected' : ''; ?>>Coordinador FCT</option>
                    </select>
                </div>
                
                <input type="hidden" name="paso" value="2">
                
                <button type="submit" class="btn btn-primary btn-block">
                    Continuar
                </button>
            </form>
            
            <?php elseif ($paso == 2 && !empty($rolSeleccionado)): ?>
            <!-- PASO 2: Formulario completo según rol -->
            <div class="pasos-info">Paso 2 de 2: Completa tus datos (<?php echo $rolSeleccionado === 'ESTUDIANTE' ? 'Estudiante' : ($rolSeleccionado === 'TUTOR_EMPRESA' ? 'Tutor de Empresa' : 'Coordinador'); ?>)</div>
            
            <form action="../controladores/AuthController.php?accion=registrar" method="POST">
                <input type="hidden" name="rol" value="<?php echo htmlspecialchars($rolSeleccionado); ?>">
                
                <!-- Datos básicos para todos -->
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre completo *</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        class="form-control" 
                        placeholder="Tu nombre completo"
                        value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Correo electrónico *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="correo@ejemplo.com"
                        value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        class="form-control" 
                        placeholder="666123456"
                        value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Mínimo 8 caracteres"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirmar_password" class="form-label">Confirmar contraseña *</label>
                    <input 
                        type="password" 
                        id="confirmar_password" 
                        name="confirmar_password" 
                        class="form-control" 
                        placeholder="Repite la contraseña"
                        required
                    >
                </div>
                
                <?php if ($rolSeleccionado === 'ESTUDIANTE'): ?>
                <!-- Campos para Estudiante -->
                <div class="campos-rol">
                    <div class="form-group">
                        <label for="dni" class="form-label">DNI *</label>
                        <input 
                            type="text" 
                            id="dni" 
                            name="dni" 
                            class="form-control" 
                            placeholder="12345678A"
                            value="<?php echo htmlspecialchars($datos['dni'] ?? ''); ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="ciclo_formativo" class="form-label">Ciclo Formativo *</label>
                        <select id="ciclo_formativo" name="ciclo_formativo" class="form-control" required>
                            <option value="">Selecciona tu ciclo</option>
                            <option value="Desarrollo de Aplicaciones Web" <?php echo ($datos['ciclo_formativo'] ?? '') === 'Desarrollo de Aplicaciones Web' ? 'selected' : ''; ?>>Desarrollo de Aplicaciones Web</option>
                            <option value="Desarrollo de Aplicaciones Multiplataforma" <?php echo ($datos['ciclo_formativo'] ?? '') === 'Desarrollo de Aplicaciones Multiplataforma' ? 'selected' : ''; ?>>Desarrollo de Aplicaciones Multiplataforma</option>
                            <option value="Administración de Sistemas Informáticos" <?php echo ($datos['ciclo_formativo'] ?? '') === 'Administración de Sistemas Informáticos' ? 'selected' : ''; ?>>Administración de Sistemas Informáticos</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="grupo" class="form-label">Grupo</label>
                        <input 
                            type="text" 
                            id="grupo" 
                            name="grupo" 
                            class="form-control" 
                            placeholder="2º DAW"
                            value="<?php echo htmlspecialchars($datos['grupo'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="anio_academico" class="form-label">Año Académico *</label>
                        <select id="anio_academico" name="anio_academico" class="form-control" required>
                            <option value="">Selecciona el año</option>
                            <option value="2024-2025" <?php echo ($datos['anio_academico'] ?? '') === '2024-2025' ? 'selected' : ''; ?>>2024-2025</option>
                            <option value="2025-2026" <?php echo ($datos['anio_academico'] ?? '') === '2025-2026' ? 'selected' : ''; ?>>2025-2026</option>
                        </select>
                    </div>
                </div>
                
                <?php elseif ($rolSeleccionado === 'TUTOR_EMPRESA'): ?>
                <!-- Campos para Tutor de Empresa -->
                <div class="campos-rol">
                    <div class="form-group">
                        <label for="empresa_id" class="form-label">Empresa</label>
                        <select id="empresa_id" name="empresa_id" class="form-control">
                            <option value="">Selecciona tu empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?php echo $empresa['id']; ?>" <?php echo ($datos['empresa_id'] ?? '') == $empresa['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($empresa['razon_social']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input 
                            type="text" 
                            id="cargo" 
                            name="cargo" 
                            class="form-control" 
                            placeholder="Tu cargo en la empresa"
                            value="<?php echo htmlspecialchars($datos['cargo'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="departamento" class="form-label">Departamento</label>
                        <input 
                            type="text" 
                            id="departamento" 
                            name="departamento" 
                            class="form-control" 
                            placeholder="Tu departamento"
                            value="<?php echo htmlspecialchars($datos['departamento'] ?? ''); ?>"
                        >
                    </div>
                </div>
                
                <?php elseif ($rolSeleccionado === 'COORDINADOR'): ?>
                <!-- Campos para Coordinador -->
                <div class="campos-rol">
                    <div class="form-group">
                        <label for="centro_educativo" class="form-label">Centro Educativo *</label>
                        <input 
                            type="text" 
                            id="centro_educativo" 
                            name="centro_educativo" 
                            class="form-control" 
                            placeholder="Nombre del centro"
                            value="<?php echo htmlspecialchars($datos['centro_educativo'] ?? ''); ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="departamento" class="form-label">Departamento</label>
                        <input 
                            type="text" 
                            id="departamento" 
                            name="departamento" 
                            class="form-control" 
                            placeholder="Tu departamento"
                            value="<?php echo htmlspecialchars($datos['departamento'] ?? ''); ?>"
                        >
                    </div>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Crear Cuenta
                </button>
                
                <a href="registro.php" class="btn btn-secondary btn-block mt-10">
                    Volver
                </a>
            </form>
            
            <?php else: ?>
            <!-- Si no hay rol seleccionado, volver al paso 1 -->
            <?php header('Location: registro.php'); exit; ?>
            <?php endif; ?>
            
            <div class="auth-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
