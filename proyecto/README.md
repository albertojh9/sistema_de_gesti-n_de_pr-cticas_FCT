# 🎓 Sistema de Gestión de Prácticas FCT - Sprint 1

**Proyecto Final de Ciclo - 2º DAW**  
**Autor:** Alberto Jiménez Hernández  
**Centro:** IES Castelar, Badajoz  
**Fecha:** Noviembre 2025

---

## 📋 Descripción del Proyecto

Sistema web para la gestión integral de prácticas formativas en centros de trabajo (FCT). Digitaliza y automatiza el proceso completo de coordinación de prácticas, desde la publicación de ofertas hasta la evaluación final de los estudiantes.

### ✨ Funcionalidades Implementadas (Sprint 1)

✅ **HU-01: Autenticación de Usuarios**
- Login seguro con email y contraseña
- Gestión de sesiones PHP
- Redirección automática según rol
- Control de acceso basado en roles (RBAC)

✅ **HU-02: Registro de Fichas de Seguimiento**
- Estudiantes registran actividades diarias
- Selección de competencias trabajadas
- Valoración personal del día (1-5 estrellas)
- Registro de dificultades encontradas
- Edición permitida hasta validación

✅ **HU-03: Validación de Fichas**
- Tutores revisan fichas pendientes
- Aprobación o rechazo con comentarios
- Actualización automática de horas acumuladas
- Sistema de notificaciones

---

## 🚀 Instalación Rápida

### Requisitos
- PHP 7.4+, MySQL 5.7+, Apache/Nginx

### Pasos
1. Copiar proyecto a htdocs (XAMPP) o /var/www/html (Linux)
2. Importar `src/sql/bbdd.sql` y `src/sql/datos_iniciales.sql` en phpMyAdmin
3. Editar `src/www/config/config.php` (ajustar BASE_URL y credenciales DB)
4. Acceder a http://localhost/sistema-fct/src/www/

## 👥 Usuarios de Prueba

**Estudiante:** carlos.martinez@ejemplo.com / password123  
**Tutor:** tutor.empresa1@techcorp.com / password123  
**Coordinador:** coordinador@iescastelar.es / password123

---

## 📁 Documentación

- **Manual de Instalación:** `/doc/manual_instalacion.md`
- **Product Backlog:** `/doc/analisis/product_backlog.md`
- **Sprint 1 Log:** `/doc/sprints/sprint1.log`

---

**Sistema FCT v1.0 - Sprint 1 ✅**
