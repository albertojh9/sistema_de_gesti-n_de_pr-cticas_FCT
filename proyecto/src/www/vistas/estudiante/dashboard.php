<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Dashboard - Sistema FCT</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container header-content">
            <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php" class="logo">
                Sistema FCT
            </a>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="<?php echo BASE_URL; ?>/controladores/auth.php?action=logout" class="btn-logout">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <h1 style="margin-bottom: 2rem;">Mi Dashboard</h1>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($asignacion): ?>
                <!-- Información de la práctica -->
                <div class="card">
                    <h2 class="card-header">Mi Práctica</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $asignacion['horas_realizadas']; ?></div>
                            <div class="stat-label">Horas Realizadas</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $asignacion['horas_requeridas']; ?></div>
                            <div class="stat-label">Horas Totales</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">
                                <?php echo round(($asignacion['horas_realizadas'] / $asignacion['horas_requeridas']) * 100); ?>%
                            </div>
                            <div class="stat-label">Progreso</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo count($fichas); ?></div>
                            <div class="stat-label">Fichas Registradas</div>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <p><strong>Empresa:</strong> <?php echo htmlspecialchars($asignacion['empresa_nombre']); ?></p>
                        <p><strong>Tutor:</strong> <?php echo htmlspecialchars($asignacion['tutor_nombre']); ?></p>
                        <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($asignacion['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($asignacion['fecha_fin'])); ?></p>
                    </div>
                </div>

                <!-- Botón nueva ficha -->
                <div style="margin-bottom: 2rem;">
                    <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php?action=nueva_ficha" class="btn btn-primary">
                        ➕ Nueva Ficha de Seguimiento
                    </a>
                </div>

                <!-- Mis Fichas -->
                <div class="card">
                    <h2 class="card-header">Mis Fichas de Seguimiento</h2>
                    
                    <?php if (empty($fichas)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <p>Aún no has registrado ninguna ficha</p>
                            <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php?action=nueva_ficha" class="btn btn-primary mt-2">
                                Crear mi primera ficha
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="ficha-list">
                            <?php foreach ($fichas as $ficha): ?>
                                <div class="ficha-item">
                                    <div class="ficha-info">
                                        <div class="ficha-date">
                                            <?php echo $ficha['fecha_formateada']; ?> 
                                            - <?php echo $ficha['hora_entrada_formateada']; ?> a <?php echo $ficha['hora_salida_formateada']; ?>
                                            (<?php echo $ficha['horas_dia']; ?>h)
                                        </div>
                                        <div class="ficha-description">
                                            <?php echo htmlspecialchars($ficha['descripcion']); ?>
                                        </div>
                                        <div style="margin-top: 0.5rem;">
                                            <?php
                                            $estado_class = '';
                                            $estado_texto = '';
                                            switch ($ficha['estado']) {
                                                case 'PENDIENTE':
                                                    $estado_class = 'badge-pending';
                                                    $estado_texto = 'Pendiente';
                                                    break;
                                                case 'VALIDADA':
                                                    $estado_class = 'badge-validated';
                                                    $estado_texto = 'Validada';
                                                    break;
                                                case 'RECHAZADA':
                                                    $estado_class = 'badge-rejected';
                                                    $estado_texto = 'Rechazada';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $estado_class; ?>">
                                                <?php echo $estado_texto; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ficha-actions">
                                        <?php if ($ficha['estado'] === 'PENDIENTE'): ?>
                                            <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php?action=editar_ficha&id=<?php echo $ficha['id']; ?>" 
                                               class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                ✏️ Editar
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php?action=ver_ficha&id=<?php echo $ficha['id']; ?>" 
                                           class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                            👁️ Ver
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </main>
</body>
</html>
