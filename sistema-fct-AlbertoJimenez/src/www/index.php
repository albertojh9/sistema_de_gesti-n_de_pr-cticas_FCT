<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Punto de entrada principal
 * 
 * @author Alberto Jiménez Hernández
 */

session_start();

// Si está logueado, ir al dashboard
if (isset($_SESSION['logueado']) && $_SESSION['logueado']) {
    header('Location: vistas/dashboard.php');
} else {
    // Si no, ir al login
    header('Location: vistas/login.php');
}
exit;
?>
