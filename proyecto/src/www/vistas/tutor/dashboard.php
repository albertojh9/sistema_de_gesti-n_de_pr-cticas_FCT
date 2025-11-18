<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tutor - Sistema FCT</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="<?php echo BASE_URL; ?>/controladores/tutor.php" class="logo">Sistema FCT</a>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="<?php echo BASE_URL; ?>/controladores/auth.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1 style="margin-bottom: 2rem;">Dashboard del Tutor</h1>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($estudiantes); ?></div>
                    <div class="stat-label">Estudiantes Asignados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($fichas_pendientes); ?></div>
                    <div class="stat-label">Fichas Pendientes</div>
                </div>
            </div>

            <!-- Fichas Pendientes de Validación -->
            <div class="card">
                <h2 class="card-header">
                    Fichas Pendientes de Validación
                    <?php if (count($fichas_pendientes) > 0): ?>
                        <span class="badge badge-pending" style="margin-left: 1rem;">
                            <?php echo count($fichas_pendientes); ?> pendientes
                        </span>
                    <?php endif; ?>
                </h2>

                <?php if (empty($fichas_pendientes)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">✓</div>
                        <p>No hay fichas pendientes de validación</p>
                    </div>
                <?php else: ?>
                    <div class="ficha-list">
                        <?php foreach ($fichas_pendientes as $ficha): ?>
                            <div class="ficha-item">
                                <div class="ficha-info">
                                    <div style="font-weight: 600; color: var(--primary-color); margin-bottom: 0.25rem;">
                                        <?php echo htmlspecialchars($ficha['estudiante_nombre']); ?>
                                    </div>
                                    <div class="ficha-date">
                                        <?php echo $ficha['fecha_formateada']; ?> - 
                                        <?php echo $ficha['hora_entrada_formateada']; ?> a <?php echo $ficha['hora_salida_formateada']; ?>
                                        (<?php echo $ficha['horas_dia']; ?>h)
                                    </div>
                                    <div class="ficha-description">
                                        <?php echo htmlspecialchars(substr($ficha['descripcion'], 0, 100)); ?>...
                                    </div>
                                </div>
                                <div class="ficha-actions">
                                    <a href="<?php echo BASE_URL; ?>/controladores/tutor.php?action=validar_ficha&id=<?php echo $ficha['id']; ?>" 
                                       class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        Validar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <a href="<?php echo BASE_URL; ?>/controladores/tutor.php?action=fichas_pendientes" class="btn btn-primary">
                            Ver Todas las Fichas Pendientes
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mis Estudiantes -->
            <div class="card">
                <h2 class="card-header">Mis Estudiantes</h2>

                <?php if (empty($estudiantes)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">👥</div>
                        <p>No tienes estudiantes asignados actualmente</p>
                    </div>
                <?php else: ?>
                    <div class="estudiantes-grid">
                        <?php foreach ($estudiantes as $est): ?>
                            <div class="estudiante-card">
                                <div class="estudiante-header">
                                    <div class="estudiante-avatar">
                                        <?php echo strtoupper(substr($est['estudiante_nombre'], 0, 1)); ?>
                                    </div>
                                    <div class="estudiante-info">
                                        <h3><?php echo htmlspecialchars($est['estudiante_nombre']); ?></h3>
                                        <p><?php echo htmlspecialchars($est['ciclo_formativo']); ?></p>
                                    </div>
                                </div>

                                <div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill" style="width: <?php echo $est['porcentaje_progreso']; ?>%"></div>
                                    </div>
                                    <div class="progress-info">
                                        <span><?php echo $est['horas_realizadas']; ?>h / <?php echo $est['horas_requeridas']; ?>h</span>
                                        <span><?php echo $est['porcentaje_progreso']; ?>%</span>
                                    </div>
                                </div>

                                <?php if ($est['fichas_pendientes'] > 0): ?>
                                    <div style="padding: 0.5rem; background-color: #fef3c7; border-radius: 0.375rem; font-size: 0.875rem;">
                                        <strong><?php echo $est['fichas_pendientes']; ?></strong> ficha<?php echo $est['fichas_pendientes'] > 1 ? 's' : ''; ?> pendiente<?php echo $est['fichas_pendientes'] > 1 ? 's' : ''; ?>
                                    </div>
                                <?php endif; ?>

                                <a href="<?php echo BASE_URL; ?>/controladores/tutor.php?action=ver_estudiante&id=<?php echo $est['id']; ?>" 
                                   class="btn btn-primary btn-block">
                                    Ver Detalle
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <a href="<?php echo BASE_URL; ?>/controladores/tutor.php?action=mis_estudiantes" class="btn btn-primary">
                            Ver Todos los Estudiantes
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
