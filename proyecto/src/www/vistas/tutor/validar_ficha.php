<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Ficha - Sistema FCT</title>
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
            <h1 style="margin-bottom: 2rem;">Validar Ficha de Seguimiento</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div style="padding: 1rem; background-color: var(--light-bg); border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <strong>Estudiante:</strong> <?php echo htmlspecialchars($ficha['estudiante_nombre']); ?>
                </div>

                <div style="display: grid; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <strong style="color: var(--text-secondary);">Fecha:</strong><br>
                        <?php echo $ficha['fecha_formateada']; ?>
                    </div>

                    <div>
                        <strong style="color: var(--text-secondary);">Horario:</strong><br>
                        <?php echo date('H:i', strtotime($ficha['hora_entrada'])); ?> - 
                        <?php echo date('H:i', strtotime($ficha['hora_salida'])); ?>
                        <strong style="color: var(--primary-color);">(<?php echo $ficha['horas_dia']; ?> horas)</strong>
                    </div>

                    <div>
                        <strong style="color: var(--text-secondary);">Descripción de Actividades:</strong><br>
                        <div style="padding: 1rem; background-color: var(--light-bg); border-radius: 0.5rem; margin-top: 0.5rem; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($ficha['descripcion'])); ?>
                        </div>
                    </div>

                    <?php if (!empty($competencias)): ?>
                        <div>
                            <strong style="color: var(--text-secondary);">Competencias Trabajadas:</strong><br>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                                <?php foreach ($competencias as $comp): ?>
                                    <div style="padding: 0.5rem 1rem; background-color: var(--light-bg); border-radius: 0.5rem; border-left: 3px solid var(--primary-color);">
                                        <strong><?php echo htmlspecialchars($comp['nombre']); ?></strong><br>
                                        <span style="font-size: 0.875rem; color: var(--text-secondary);">
                                            <?php echo htmlspecialchars($comp['descripcion']); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($ficha['dificultades'])): ?>
                        <div>
                            <strong style="color: var(--text-secondary);">Dificultades Encontradas:</strong><br>
                            <div style="padding: 1rem; background-color: #fef3c7; border-radius: 0.5rem; margin-top: 0.5rem; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($ficha['dificultades'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($ficha['valoracion']): ?>
                        <div>
                            <strong style="color: var(--text-secondary);">Valoración del estudiante:</strong><br>
                            <div style="margin-top: 0.5rem; color: var(--warning-color); font-size: 1.5rem;">
                                <?php echo str_repeat('★', $ficha['valoracion']) . str_repeat('☆', 5 - $ficha['valoracion']); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario de validación -->
                <form action="<?php echo BASE_URL; ?>/controladores/tutor.php?action=procesar_validacion" method="POST" id="validacionForm">
                    <input type="hidden" name="ficha_id" value="<?php echo $ficha['id']; ?>">

                    <div class="form-group">
                        <label for="comentarios" class="form-label">Comentarios del Tutor</label>
                        <textarea 
                            id="comentarios" 
                            name="comentarios" 
                            class="form-textarea" 
                            rows="4"
                            placeholder="Añade tus comentarios sobre el trabajo realizado (obligatorio si rechazas la ficha)..."
                        ></textarea>
                        <p class="text-muted" style="font-size: 0.875rem; margin-top: 0.25rem;">
                            Los comentarios son obligatorios si rechazas la ficha
                        </p>
                    </div>

                    <div class="btn-group">
                        <a href="<?php echo BASE_URL; ?>/controladores/tutor.php?action=fichas_pendientes" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" name="accion" value="rechazar" class="btn btn-danger" onclick="return confirm('¿Seguro que quieres rechazar esta ficha?');">
                            ✗ Rechazar Ficha
                        </button>
                        <button type="submit" name="accion" value="aprobar" class="btn btn-success">
                            ✓ Aprobar Ficha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Validar que si se rechaza debe haber comentarios
        document.getElementById('validacionForm').addEventListener('submit', function(e) {
            const accion = e.submitter.value;
            const comentarios = document.getElementById('comentarios').value.trim();
            
            if (accion === 'rechazar' && comentarios === '') {
                e.preventDefault();
                alert('Debes añadir comentarios para rechazar la ficha');
                document.getElementById('comentarios').focus();
            }
        });
    </script>
</body>
</html>
