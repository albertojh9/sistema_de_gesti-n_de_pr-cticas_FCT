<!-- 
============================================================================
VISTA DE LOGIN
============================================================================

Este archivo es una VISTA del patrón MVC.

¿Qué hace una Vista?
- Muestra la información al usuario (HTML)
- Recibe datos del Controlador para mostrar
- NO contiene lógica de negocio compleja
- Solo tiene lógica de presentación (if para mostrar/ocultar)

Este archivo específico:
- Muestra el formulario de login
- Muestra mensajes de error si los hay
- Envía los datos al controlador auth.php
-->

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- 
    ========================================================================
    META TAGS Y CONFIGURACIÓN
    ========================================================================
    -->
    
    <!-- Codificación de caracteres - UTF-8 para soportar tildes y ñ -->
    <meta charset="UTF-8">
    
    <!-- 
    Viewport: Importante para responsive design
    - width=device-width: El ancho será el del dispositivo
    - initial-scale=1.0: Sin zoom inicial
    Sin esto, la página se vería diminuta en móviles
    -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Título que aparece en la pestaña del navegador -->
    <title>Iniciar Sesión - Sistema FCT</title>
    
    <!-- 
    Enlace a la hoja de estilos CSS
    
    <?php echo BASE_URL; ?> - Esto es PHP embebido en HTML
    - Se ejecuta en el servidor
    - El navegador solo ve la URL resultante
    - Ejemplo resultado: href="http://localhost/dwes/proyecto/src/www/css/styles.css"
    -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <!-- 
    ========================================================================
    CONTENEDOR PRINCIPAL
    ========================================================================
    
    Las clases CSS (login-container, login-card, etc.) están definidas
    en styles.css y dan el aspecto visual a la página
    -->
    <div class="login-container">
        <div class="login-card">
            
            <!-- Logo y título de la aplicación -->
            <div class="login-logo">
                <h1>Sistema FCT</h1>
                <p>Gestión de Prácticas Formativas</p>
            </div>

            <!-- 
            ================================================================
            MENSAJES DE ERROR/ALERTA
            ================================================================
            
            Estos bloques muestran mensajes según diferentes situaciones.
            Usan la sintaxis alternativa de PHP para estructuras de control:
            if (): ... endif;  (más legible en HTML que if { })
            -->

            <!-- 
            Mensaje de error de login
            
            isset(): Comprueba si la variable existe
            $_SESSION['error'] se establece en el controlador cuando falla el login
            -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    /**
                     * htmlspecialchars(): Convierte caracteres especiales a entidades HTML
                     * 
                     * ¿Por qué usarlo?
                     * - Previene ataques XSS (Cross-Site Scripting)
                     * - Si alguien pone <script>alert('hack')</script> como email
                     * - Sin htmlspecialchars se ejecutaría el JavaScript
                     * - Con htmlspecialchars se muestra como texto
                     * 
                     * Convierte: < > " ' & a &lt; &gt; &quot; &#039; &amp;
                     */
                    echo htmlspecialchars($_SESSION['error']); 
                    
                    /**
                     * unset(): Elimina la variable
                     * 
                     * Borramos el error después de mostrarlo para que:
                     * - No aparezca otra vez si recarga la página
                     * - No se acumulen errores
                     */
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- 
            Mensaje de timeout de sesión
            
            $_GET['timeout'] viene de la URL: ?timeout=1
            Se establece cuando la sesión expira por inactividad
            -->
            <?php if (isset($_GET['timeout'])): ?>
                <div class="alert alert-warning">
                    Tu sesión ha expirado. Por favor, inicia sesión nuevamente.
                </div>
            <?php endif; ?>

            <!-- 
            Mensaje de acceso denegado
            
            Se muestra cuando el usuario intenta acceder a una página
            para la que no tiene permisos (ej: estudiante a página de tutor)
            -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'acceso_denegado'): ?>
                <div class="alert alert-error">
                    No tienes permisos para acceder a esa página.
                </div>
            <?php endif; ?>

            <!-- 
            ================================================================
            FORMULARIO DE LOGIN
            ================================================================
            
            action: URL donde se envían los datos
            method: POST porque estamos enviando datos sensibles
            
            ¿Por qué POST y no GET?
            - GET pone los datos en la URL (visible, se guarda en historial)
            - POST envía los datos en el cuerpo de la petición (más seguro)
            - NUNCA envíes contraseñas por GET
            -->
            <form action="<?php echo BASE_URL; ?>/controladores/auth.php?action=login" method="POST">
                
                <!-- 
                Campo de email
                
                form-group: Contenedor para agrupar label + input
                -->
                <div class="form-group">
                    <!-- 
                    Label: Etiqueta descriptiva del campo
                    for="email": Conecta el label con el input (al hacer clic en el label, se enfoca el input)
                    -->
                    <label for="email" class="form-label">Email</label>
                    
                    <!-- 
                    Input de email
                    
                    type="email": 
                    - Valida formato de email en el navegador
                    - En móviles muestra teclado con @
                    
                    id="email": Identificador único (para el label y JavaScript)
                    name="email": Nombre con el que llega al servidor ($_POST['email'])
                    
                    required: El formulario no se envía si está vacío
                    autofocus: El cursor aparece aquí al cargar la página
                    placeholder: Texto de ejemplo que desaparece al escribir
                    -->
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="correo@ejemplo.com"
                        required
                        autofocus
                    >
                </div>

                <!-- Campo de contraseña -->
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    
                    <!-- 
                    Input de contraseña
                    
                    type="password": 
                    - Muestra puntos/asteriscos en lugar del texto
                    - Por seguridad para que nadie vea lo que escribes
                    
                    minlength="8": Validación HTML5 - mínimo 8 caracteres
                    (También se valida en el servidor por si el navegador no lo soporta)
                    -->
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="********"
                        required
                        minlength="8"
                    >
                </div>

                <!-- 
                Botón de envío
                
                type="submit": Al hacer clic, envía el formulario
                btn-block: Clase CSS para que ocupe todo el ancho
                -->
                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión
                </button>

                <!-- Enlace de recuperación de contraseña (sin implementar) -->
                <p class="text-center text-muted mt-3" style="font-size: 0.875rem;">
                    <a href="#" style="color: var(--primary-color);">¿Olvidaste tu contraseña?</a>
                </p>
            </form>

            <!-- 
            ================================================================
            INFORMACIÓN DE USUARIOS DE PRUEBA
            ================================================================
            
            Esto es solo para desarrollo/pruebas.
            En producción se eliminaría.
            -->
            <div class="mt-3" style="padding: 1rem; background-color: var(--light-bg); border-radius: 0.375rem; font-size: 0.875rem;">
                <strong>Usuarios de prueba:</strong><br>
                <strong>Estudiante:</strong> carlos.martinez@ejemplo.com<br>
                <strong>Tutor:</strong> tutor.empresa1@techcorp.com<br>
                <strong>Contraseña:</strong> password
            </div>
        </div>
    </div>
</body>
</html>
