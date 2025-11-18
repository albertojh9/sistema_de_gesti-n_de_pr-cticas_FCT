# Manual de Instalación - Sistema FCT

## Requisitos del Sistema

### Software Necesario
- **PHP:** 7.4 o superior
- **MySQL/MariaDB:** 5.7 o superior
- **Servidor Web:** Apache 2.4+ o Nginx
- **Navegador Web:** Chrome, Firefox, Safari o Edge (últimas versiones)

### Extensiones PHP Requeridas
- PDO
- PDO_MySQL
- mbstring
- openssl

---

## Instalación Paso a Paso

### 1. Preparar el Entorno

#### En Windows (XAMPP)
1. Descargar e instalar XAMPP desde https://www.apachefriends.org/
2. Iniciar los servicios Apache y MySQL desde el panel de control de XAMPP

#### En Linux
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 php php-mysql mysql-server

# CentOS/RHEL
sudo yum install httpd php php-mysql mariadb-server
```

#### En macOS (MAMP)
1. Descargar e instalar MAMP desde https://www.mamp.info/
2. Iniciar MAMP y verificar que Apache y MySQL estén corriendo

---

### 2. Clonar/Descargar el Proyecto

```bash
# Opción 1: Si tienes el proyecto en Git
git clone [URL_DEL_REPOSITORIO] sistema-fct
cd sistema-fct

# Opción 2: Si tienes un archivo ZIP
unzip sistema-fct.zip
cd sistema-fct
```

---

### 3. Configurar el Servidor Web

#### Apache (htdocs/www)

**Windows (XAMPP):**
```
Copiar la carpeta del proyecto a: C:\xampp\htdocs\sistema-fct
```

**Linux:**
```bash
sudo cp -r sistema-fct /var/www/html/
sudo chown -R www-data:www-data /var/www/html/sistema-fct
sudo chmod -R 755 /var/www/html/sistema-fct
```

**macOS (MAMP):**
```
Copiar la carpeta a: /Applications/MAMP/htdocs/sistema-fct
```

---

### 4. Crear la Base de Datos

#### Opción 1: Mediante phpMyAdmin
1. Acceder a phpMyAdmin: http://localhost/phpmyadmin
2. Hacer clic en "Nuevo" para crear una base de datos
3. Nombre: `sistema_fct`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Ir a la pestaña "Importar"
6. Seleccionar el archivo `src/sql/bbdd.sql`
7. Hacer clic en "Continuar"
8. Seleccionar el archivo `src/sql/datos_iniciales.sql`
9. Hacer clic en "Continuar"

#### Opción 2: Mediante línea de comandos
```bash
# Acceder a MySQL
mysql -u root -p

# Dentro de MySQL
source /ruta/completa/sistema-fct/src/sql/bbdd.sql
source /ruta/completa/sistema-fct/src/sql/datos_iniciales.sql
exit;
```

---

### 5. Configurar la Conexión a la Base de Datos

Editar el archivo `src/www/config/config.php`:

```php
// Configuración de la base de datos
define('DB_HOST', 'localhost');        // Host de MySQL
define('DB_NAME', 'sistema_fct');      // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario de MySQL
define('DB_PASS', '');                 // Contraseña de MySQL (vacía en XAMPP por defecto)
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('BASE_URL', 'http://localhost/sistema-fct/src/www');  // ← AJUSTAR SEGÚN TU INSTALACIÓN
```

**Importante:** Ajusta `BASE_URL` según tu configuración:
- XAMPP Windows: `http://localhost/sistema-fct/src/www`
- LAMP Linux: `http://localhost/sistema-fct/src/www`
- MAMP macOS: `http://localhost:8888/sistema-fct/src/www`

---

### 6. Verificar Permisos (Solo Linux/macOS)

```bash
# Asegurarse de que el servidor web pueda leer los archivos
sudo chown -R www-data:www-data /var/www/html/sistema-fct
sudo chmod -R 755 /var/www/html/sistema-fct
```

---

### 7. Acceder al Sistema

Abrir el navegador web y acceder a:
```
http://localhost/sistema-fct/src/www/
```

---

## Usuarios de Prueba

El sistema viene con usuarios predefinidos para pruebas:

### Estudiante
- **Email:** carlos.martinez@ejemplo.com
- **Contraseña:** password123
- **Descripción:** Puede crear y gestionar fichas de seguimiento

### Tutor de Empresa
- **Email:** tutor.empresa1@techcorp.com
- **Contraseña:** password123
- **Descripción:** Puede validar fichas de estudiantes asignados

### Coordinador FCT
- **Email:** coordinador@iescastelar.es
- **Contraseña:** password123
- **Descripción:** Gestión completa del sistema (preparado para Sprint 2)

---

## Solución de Problemas Comunes

### Problema 1: "No se puede conectar a la base de datos"
**Causas posibles:**
- MySQL no está iniciado
- Credenciales incorrectas en config.php
- La base de datos no existe

**Solución:**
1. Verificar que MySQL esté corriendo
2. Comprobar las credenciales en `config.php`
3. Verificar que la base de datos `sistema_fct` exista

### Problema 2: Error 404 - Página no encontrada
**Causa:** BASE_URL incorrecta

**Solución:**
Editar `src/www/config/config.php` y ajustar BASE_URL según tu configuración.

### Problema 3: "Fatal error: Class 'PDO' not found"
**Causa:** Extensión PDO no habilitada

**Solución:**
Editar `php.ini` y descomentar:
```ini
extension=pdo_mysql
```

Reiniciar Apache.

### Problema 4: CSS no se carga correctamente
**Causa:** Ruta BASE_URL incorrecta

**Solución:**
Verificar que BASE_URL en `config.php` apunte correctamente a la carpeta `/src/www/`

### Problema 5: "Access denied for user 'root'@'localhost'"
**Causa:** Contraseña de MySQL incorrecta

**Solución en XAMPP (sin contraseña):**
```php
define('DB_PASS', '');  // Dejar vacío
```

**Solución en producción:**
```php
define('DB_PASS', 'tu_contraseña_mysql');
```

---

## Configuración para Producción

### 1. Seguridad

Editar `src/www/config/config.php`:

```php
// DESACTIVAR errores en producción
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Habilitar logging de errores
ini_set('log_errors', 1);
ini_set('error_log', '/ruta/logs/php-errors.log');
```

### 2. Crear Usuario MySQL Específico

```sql
CREATE USER 'fct_user'@'localhost' IDENTIFIED BY 'contraseña_segura_aqui';
GRANT SELECT, INSERT, UPDATE, DELETE ON sistema_fct.* TO 'fct_user'@'localhost';
FLUSH PRIVILEGES;
```

Actualizar `config.php`:
```php
define('DB_USER', 'fct_user');
define('DB_PASS', 'contraseña_segura_aqui');
```

### 3. Configurar HTTPS

```apache
# En el archivo .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 4. Backup Automático

Crear un script de backup:
```bash
#!/bin/bash
mysqldump -u root -p sistema_fct > /backups/sistema_fct_$(date +%Y%m%d).sql
```

Agregar a crontab:
```
0 2 * * * /ruta/backup.sh
```

---

## Verificación de Instalación

### Checklist Final

- [ ] Apache/Nginx está corriendo
- [ ] MySQL está corriendo
- [ ] Base de datos `sistema_fct` creada
- [ ] Tablas creadas correctamente (12 tablas)
- [ ] Datos iniciales cargados (6 usuarios)
- [ ] Archivo config.php configurado
- [ ] BASE_URL correcta
- [ ] Login funciona correctamente
- [ ] Estudiante puede crear fichas
- [ ] Tutor puede validar fichas

---

## Soporte Técnico

Para problemas o dudas:
- **Desarrollador:** Alberto Jiménez Hernández
- **Profesor:** Miguel Jaque Barbero
- **Centro:** IES Castelar - Badajoz

---

## Información Adicional

- **Versión:** 1.0 (Sprint 1)
- **Fecha:** Noviembre 2025
- **Licencia:** Proyecto Educativo FCT
- **Documentación Completa:** Ver `/doc/manual_usuario.md` y `/doc/manual_programador.md`

---

_Última actualización: 17/11/2025_
