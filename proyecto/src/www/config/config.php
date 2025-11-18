<?php
/**
 * ============================================================================
 * ARCHIVO DE CONFIGURACIÓN PRINCIPAL
 * ============================================================================
 * 
 * Este archivo contiene todas las constantes de configuración del sistema.
 * Se carga al inicio de cada petición y define:
 * - Conexión a base de datos
 * - URLs de la aplicación
 * - Configuración de sesiones
 * - Zona horaria
 * - Modo de depuración
 */

// ============================================================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================================================

/**
 * DB_HOST: Servidor donde está la base de datos
 * - 'localhost' significa que MySQL está en el mismo servidor que Apache
 * - En producción podría ser una IP o dominio diferente
 */
define('DB_HOST', 'localhost');

/**
 * DB_NAME: Nombre de la base de datos
 * - Debe coincidir exactamente con el nombre creado en phpMyAdmin
 * - Es sensible a mayúsculas/minúsculas en Linux
 */
define('DB_NAME', 'sistemas_fct');

/**
 * DB_USER: Usuario de MySQL
 * - 'root' es el usuario por defecto en XAMPP (solo para desarrollo)
 * - En producción NUNCA uses root, crea un usuario específico
 */
define('DB_USER', 'root');

/**
 * DB_PASS: Contraseña de MySQL
 * - Vacía por defecto en XAMPP
 * - En producción SIEMPRE usa una contraseña segura
 */
define('DB_PASS', '');

/**
 * DB_CHARSET: Codificación de caracteres
 * - utf8mb4 soporta todos los caracteres Unicode (incluyendo emojis)
 * - Es el estándar recomendado para aplicaciones modernas
 */
define('DB_CHARSET', 'utf8mb4');

// ============================================================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================================================

/**
 * BASE_URL: URL base de la aplicación
 * - Se usa para construir todas las URLs del sistema
 * - Debe coincidir con la ubicación real en htdocs
 * - NO incluir barra final (/)
 * 
 * Ejemplo de uso: header('Location: ' . BASE_URL . '/index.php');
 */
define('BASE_URL', 'http://localhost/dwes/proyecto/src/www');

/**
 * APP_NAME: Nombre de la aplicación
 * - Se muestra en títulos de páginas
 * - Se puede usar en emails o notificaciones
 */
define('APP_NAME', 'Sistema FCT');

// ============================================================================
// CONFIGURACIÓN DE SESIÓN
// ============================================================================

/**
 * SESSION_TIMEOUT: Tiempo máximo de inactividad en segundos
 * - 3600 segundos = 1 hora
 * - Si el usuario no hace nada durante este tiempo, se cierra su sesión
 * - Medida de seguridad para evitar sesiones abandonadas
 */
define('SESSION_TIMEOUT', 3600);

// ============================================================================
// ZONA HORARIA
// ============================================================================

/**
 * date_default_timezone_set(): Establece la zona horaria
 * - Afecta a todas las funciones de fecha/hora de PHP
 * - 'Europe/Madrid' incluye cambio horario verano/invierno
 * - Importante para registrar correctamente las horas de las fichas
 */
date_default_timezone_set('Europe/Madrid');

// ============================================================================
// CONFIGURACIÓN DE ERRORES (SOLO DESARROLLO)
// ============================================================================

/**
 * display_errors: Muestra errores en pantalla
 * - 1 = Activado (para desarrollo)
 * - 0 = Desactivado (para producción)
 * 
 * ¡IMPORTANTE! En producción SIEMPRE debe estar en 0
 * Los errores pueden revelar información sensible
 */
ini_set('display_errors', 1);

/**
 * display_startup_errors: Muestra errores de inicio de PHP
 * - Errores que ocurren antes de que el script se ejecute
 */
ini_set('display_startup_errors', 1);

/**
 * error_reporting: Nivel de errores a reportar
 * - E_ALL = Todos los errores, warnings y notices
 * - Ayuda a detectar problemas durante el desarrollo
 */
error_reporting(E_ALL);
