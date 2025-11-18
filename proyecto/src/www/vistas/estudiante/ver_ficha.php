<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Ficha - Sistema FCT</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php" class="logo">Sistema FCT</a>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="<?php echo BASE_URL; ?>/controladores/auth.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1 style="margin-bottom: 2rem;">Detalle de Ficha</h1>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2><?php echo $ficha['fecha_formateada']; ?></h2>
                    <?php
                    $estado_class = $ficha['estado'] === 'VALIDADA' ? 'badge-validated' : 
                                   ($ficha['estado'] === 'RECHAZADA' ? 'badge-rejected' : 'badge-pending');
                    $estado_texto = $ficha['estado'] === 'VALIDADA' ? 'Validada' : 
                                   ($ficha['estado'] === 'RECHAZADA' ? 'Rechazada' : 'Pendiente');
                    ?>
                    <span class="badge <?php echo $estado_class; ?>"><?php echo $estado_texto; ?></span>
                </div>

                <div style="display: grid; gap: 1.5rem;">
                    <div>
                        <strong style="color: var(--text-secondary);">Horario:</strong><br>
                        <?php echo date('H:i', strtotime($ficha['hora_entrada'])); ?> - 
                        <?php echo date('H:i', strtotime($ficha['hora_salida'])); ?>
                        (<?php echo $ficha['horas_dia']; ?> horas)
                    </div>

                    <div>
                        <strong style="color: var(--text-secondary);">Descripción de Actividades:</strong><br>
                        <p style="margin-top: 0.5rem; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($ficha['descripcion'])); ?>
                        </p>
                    </div>

                    <?php if (!empty($competencias)): ?>
                        <div>
                            <strong style="color: var(--text-secondary);">Competencias Trabajadas:</strong><br>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php foreach ($competencias as $comp): ?>
                                    <span style="padding: 0.25rem 0.75rem; background-color: var(--light-bg); border-radius: 9999px; font-size: 0.875rem;">
                                        <?php echo htmlspecialchars($comp['nombre']); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($ficha['dificultades'])): ?>
                        <div>
                            <strong style="color: var(--text-secondary);">Dificultades:</strong><br>
                            <p style="margin-top: 0.5rem; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($ficha['dificultades'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($ficha['valoracion']): ?>
                        <div>
                            <strong style="color: var(--text-secondary);">Valoración del día:</strong><br>
                            <div style="margin-top: 0.5rem; color: var(--warning-color); font-size: 1.5rem;">
                                <?php echo str_repeat('★', $ficha['valoracion']) . str_repeat('☆', 5 - $ficha['valoracion']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($ficha['estado'] !== 'PENDIENTE'): ?>
                        <div style="padding: 1rem; background-color: var(--light-bg); border-radius: 0.5rem;">
                            <strong style="color: var(--text-secondary);">
                                Comentarios del Tutor (<?php echo htmlspecialchars($ficha['validada_por_nombre']); ?>):
                            </strong><br>
                            <p style="margin-top: 0.5rem;">
                                <?php echo !empty($ficha['comentarios_tutor']) ? nl2br(htmlspecialchars($ficha['comentarios_tutor'])) : 'Sin comentarios'; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php" class="btn btn-secondary">
                        Volver al Dashboard
                    </a>
                    <?php if ($ficha['estado'] === 'PENDIENTE'): ?>
                        <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php?action=editar_ficha&id=<?php echo $ficha['id']; ?>" class="btn btn-primary">
                            Editar Ficha
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
