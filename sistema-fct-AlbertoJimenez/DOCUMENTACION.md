# Documentación del Proyecto

## Sistema de Gestión de Prácticas FCT

**Proyecto Final de Ciclo - 2º DAW**  
**IES Castelar - Badajoz**  
**Curso 2024-2025**

---

## Índice

1. [Información General](#información-general)
2. [Tecnologías](#tecnologías)
3. [Instalación](#instalación)
4. [Funcionalidades](#funcionalidades)
5. [Usuarios de Prueba](#usuarios-de-prueba)
6. [Paleta de Colores](#paleta-de-colores)

---

## Información General

**Autor:** Alberto Jiménez Hernández  
**Versión:** 1.0 - Sprint 1  

Sistema web para gestionar las prácticas FCT de los estudiantes de ciclos formativos.

---

## Tecnologías

- **Backend:** PHP 8.x
- **Base de Datos:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3
- **Arquitectura:** MVC

---

## Instalación

### 1. Configurar Conexión

Editar `src/www/includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_fct');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 2. Configurar Servidor Web

Apuntar DocumentRoot a `src/www/`

---

## Funcionalidades

### HU-01: Registro de Usuarios
- Sistema de 2 pasos (sin JavaScript)
- Paso 1: Seleccionar rol
- Paso 2: Completar datos según rol
- Validación de contraseña segura
- Mensaje de éxito tras registro

### HU-02: Inicio de Sesión
- Login con email y contraseña
- Bloqueo tras 5 intentos fallidos
- Mensaje de sesión iniciada correctamente

---

## 👥 Usuarios de Prueba

| Email | Contraseña | Rol |
|-------|------------|-----|
| coordinador@iescastelar.es | password | Coordinador |
| carlos.martinez@alumno.iescastelar.es | password | Estudiante |
| ana.garcia@alumno.iescastelar.es | password | Estudiante |
| tutor1@techsolutions.es | password | Tutor Empresa |

---

## Paleta de Colores

La paleta de colores está definida en dos lugares:

### 1. Archivo CSS (`src/www/css/styles.css`)
Al inicio del archivo hay un comentario con todos los colores.

### 2. Guía de Estilo Visual (`doc/diseño/guia_estilo.html`)
Abre este archivo en el navegador para ver la paleta de colores de forma visual con ejemplos de cada color y su uso.

### Colores Principales

| Color | Código | Uso |
|-------|--------|-----|
| Azul principal | `#2563EB` | Botones, enlaces, navbar |
| Azul oscuro | `#1D4ED8` | Hover de botones |
| Verde éxito | `#10B981` | Mensajes de éxito |
| Rojo error | `#EF4444` | Mensajes de error |
| Gris oscuro | `#1F2937` | Texto principal |
| Gris claro | `#F3F4F6` | Fondos |

---

## Contacto

**Alberto Jiménez Hernández**  
IES Castelar - Badajoz  
2º DAW - Curso 2025-2026
