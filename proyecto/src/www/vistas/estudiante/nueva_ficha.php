<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Ficha - Sistema FCT</title>
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
            <h1 style="margin-bottom: 2rem;">Nueva Ficha de Seguimiento</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form action="<?php echo BASE_URL; ?>/controladores/estudiante.php?action=guardar_ficha" method="POST" id="fichaForm">
                    
                    <div class="form-group">
                        <label for="fecha" class="form-label">Fecha *</label>
                        <input 
                            type="date" 
                            id="fecha" 
                            name="fecha" 
                            class="form-input" 
                            value="<?php echo isset($_SESSION['form_data']['fecha']) ? $_SESSION['form_data']['fecha'] : date('Y-m-d'); ?>"
                            required
                            max="<?php echo date('Y-m-d'); ?>"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="hora_entrada" class="form-label">Hora de Entrada *</label>
                            <input 
                                type="time" 
                                id="hora_entrada" 
                                name="hora_entrada" 
                                class="form-input" 
                                value="<?php echo isset($_SESSION['form_data']['hora_entrada']) ? $_SESSION['form_data']['hora_entrada'] : '09:00'; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="hora_salida" class="form-label">Hora de Salida *</label>
                            <input 
                                type="time" 
                                id="hora_salida" 
                                name="hora_salida" 
                                class="form-input" 
                                value="<?php echo isset($_SESSION['form_data']['hora_salida']) ? $_SESSION['form_data']['hora_salida'] : '17:00'; ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción de Actividades * (mínimo 50 caracteres)</label>
                        <textarea 
                            id="descripcion" 
                            name="descripcion" 
                            class="form-textarea" 
                            rows="6"
                            required
                            minlength="50"
                            placeholder="Describe las actividades que realizaste hoy..."
                        ><?php echo isset($_SESSION['form_data']['descripcion']) ? htmlspecialchars($_SESSION['form_data']['descripcion']) : ''; ?></textarea>
                        <p class="text-muted" style="font-size: 0.875rem; margin-top: 0.25rem;">
                            <span id="charCount">0</span> / 50 caracteres mínimo
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Competencias Trabajadas</label>
                        <div class="checkbox-group">
                            <?php foreach ($competencias as $categoria => $comps): ?>
                                <?php if (!empty($comps)): ?>
                                    <div style="margin-bottom: 1rem;">
                                        <strong style="color: var(--text-secondary); font-size: 0.875rem; text-transform: uppercase;">
                                            <?php 
                                            $categoria_nombre = '';
                                            switch ($categoria) {
                                                case 'TECNICA': $categoria_nombre = 'Competencias Técnicas'; break;
                                                case 'TRANSVERSAL': $categoria_nombre = 'Competencias Transversales'; break;
                                                case 'ACTITUDINAL': $categoria_nombre = 'Competencias Actitudinales'; break;
                                            }
                                            echo $categoria_nombre;
                                            ?>
                                        </strong>
                                        <?php foreach ($comps as $comp): ?>
                                            <div class="checkbox-item">
                                                <input 
                                                    type="checkbox" 
                                                    id="comp_<?php echo $comp['id']; ?>" 
                                                    name="competencias[]" 
                                                    value="<?php echo $comp['id']; ?>"
                                                >
                                                <label for="comp_<?php echo $comp['id']; ?>" style="cursor: pointer; flex: 1;">
                                                    <strong><?php echo htmlspecialchars($comp['nombre']); ?></strong><br>
                                                    <span style="font-size: 0.875rem; color: var(--text-secondary);">
                                                        <?php echo htmlspecialchars($comp['descripcion']); ?>
                                                    </span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dificultades" class="form-label">Dificultades Encontradas (opcional)</label>
                        <textarea 
                            id="dificultades" 
                            name="dificultades" 
                            class="form-textarea" 
                            rows="3"
                            placeholder="Describe cualquier dificultad o problema que hayas encontrado..."
                        ><?php echo isset($_SESSION['form_data']['dificultades']) ? htmlspecialchars($_SESSION['form_data']['dificultades']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Valoración del Día (opcional)</label>
                        <div class="rating" id="rating">
                            <input type="radio" id="star5" name="valoracion" value="5">
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="valoracion" value="4">
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="valoracion" value="3">
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="valoracion" value="2">
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="valoracion" value="1">
                            <label for="star1">★</label>
                        </div>
                    </div>

                    <div class="btn-group">
                        <a href="<?php echo BASE_URL; ?>/controladores/estudiante.php" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Guardar Ficha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Contador de caracteres
        const descripcion = document.getElementById('descripcion');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            charCount.textContent = descripcion.value.length;
            if (descripcion.value.length >= 50) {
                charCount.style.color = 'var(--success-color)';
            } else {
                charCount.style.color = 'var(--danger-color)';
            }
        }
        
        descripcion.addEventListener('input', updateCharCount);
        updateCharCount();

        // Sistema de estrellas
        const ratingInputs = document.querySelectorAll('.rating input');
        const ratingLabels = document.querySelectorAll('.rating label');
        
        ratingInputs.forEach((input, index) => {
            input.addEventListener('change', () => {
                ratingLabels.forEach((label, labelIndex) => {
                    if (labelIndex >= index) {
                        label.style.color = 'var(--warning-color)';
                    } else {
                        label.style.color = 'var(--border-color)';
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php unset($_SESSION['form_data']); ?>
