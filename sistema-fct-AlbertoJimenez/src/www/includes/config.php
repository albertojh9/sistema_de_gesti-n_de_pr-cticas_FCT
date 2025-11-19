<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Configuración de la Base de Datos
 * 
 * @author Alberto Jiménez Hernández
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_fct');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Sistema FCT');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/sistema-fct');

// Configuración de sesión
define('SESSION_LIFETIME', 86400); // 24 horas en segundos

// Configuración de seguridad
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos en segundos
define('PASSWORD_MIN_LENGTH', 8);

// Rutas
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('MODELS_PATH', ROOT_PATH . '/modelos');
define('VIEWS_PATH', ROOT_PATH . '/vistas');
define('CONTROLLERS_PATH', ROOT_PATH . '/controladores');

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Mostrar errores (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
