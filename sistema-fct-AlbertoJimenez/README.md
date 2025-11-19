# Sistema de Gestión de Prácticas FCT

**Autor:** Alberto Jiménez Hernández  
**Centro:** IES Castelar - Badajoz  
**Curso:** 2º DAW  
**Versión:** 1.0 - Sprint 1

---

## Descripción del Proyecto

Este es un sistema web para gestionar las prácticas FCT (Formación en Centros de Trabajo) de los estudiantes de ciclos formativos. Permite a coordinadores, tutores de empresa y estudiantes llevar un control digitalizado de todo el proceso de prácticas.

---

## Sprint 1: Sistema de Autenticación

En este primer sprint se han implementado dos funcionalidades básicas:

1. **Registro de usuarios**: Permite crear cuentas de estudiante, tutor de empresa o coordinador
2. **Inicio de sesión**: Permite acceder al sistema con email y contraseña

---

## Estructura del Proyecto

```
sistema-fct-sprint1/
├── doc/                          # Documentación
│   ├── analisis/                 # Análisis del sistema
│   │   └── product_backlog.md    # Lista de historias de usuario
│   ├── diseño/                   # Diseño visual
│   │   └── guia_estilo.html      # Paleta de colores y componentes                 
│   └── sprints/                  # Logs de cada sprint
│       └── sprint1.log           # Registro del sprint 1
├── src/                          # Código fuente
│   ├── sql/                      # Scripts de base de datos
│   │   ├── bbdd.sql              # Estructura de tablas
│   │   ├── datos_iniciales.sql   # Datos básicos del sistema
│   │   └── datos_pruebas.sql     # Datos para testing
│   └── www/                      # Aplicación web
│       ├── index.php             # Punto de entrada
│       ├── includes/             # Archivos de configuración
│       ├── modelos/              # Lógica de negocio (MVC)
│       ├── vistas/               # Páginas HTML/PHP (MVC)
│       ├── controladores/        # Procesamiento de datos (MVC)
│       ├── css/                  # Estilos
│       └── js/                   # JavaScript
├── despliegue/                   # Archivos de despliegue
└── README.md                     # Este archivo
```

---

## Cómo Funciona Cada Parte

### 1. Base de Datos (src/sql/)

#### bbdd.sql
Este archivo crea la estructura de la base de datos. Contiene las tablas necesarias:

- **Usuario**: Guarda los datos de login (email, contraseña hasheada, rol)
- **Estudiante**: Datos específicos de estudiantes (DNI, ciclo formativo)
- **TutorEmpresa**: Datos de tutores (empresa, cargo)
- **Coordinador**: Datos de coordinadores (centro educativo)
- **Empresa**: Empresas colaboradoras
- **Competencia**: Lista de competencias evaluables

**Para explicar a alguien:** "Este archivo es como el plano de una casa. Define qué habitaciones (tablas) tendrá la base de datos y qué muebles (campos) irán en cada una."

#### datos_iniciales.sql
Inserta los datos básicos que el sistema necesita para funcionar:
- Competencias predefinidas para DAW
- Usuario coordinador por defecto

**Para explicar:** "Es como amueblar la casa básica. Pone lo mínimo necesario para que se pueda vivir en ella."

#### datos_pruebas.sql
Añade usuarios y empresas de ejemplo para hacer pruebas.

**Para explicar:** "Son datos de mentira para probar que todo funciona bien antes de usar datos reales."

---

### 2. Configuración (src/www/includes/)

#### config.php
Contiene todas las constantes del sistema:
- Datos de conexión a la base de datos
- Configuración de seguridad (intentos de login, tiempo de bloqueo)
- Rutas de archivos

**Para explicar:** "Es como el panel de control de la casa. Aquí se configuran todas las opciones importantes: la contraseña del WiFi, el código de la alarma, etc."

#### Database.php
Clase que gestiona la conexión a la base de datos usando el patrón **Singleton**.

**¿Qué es Singleton?** Es un patrón de diseño que asegura que solo exista UNA conexión a la base de datos en toda la aplicación. Esto ahorra recursos.

**Para explicar:** "Imagina que la base de datos es una fuente de agua. En lugar de que cada grifo tenga su propia tubería directa (desperdicio), todos los grifos comparten una única tubería central."

---

### 3. Modelo (src/www/modelos/)

#### Usuario.php
Esta clase contiene toda la **lógica de negocio** relacionada con usuarios:

- `registrar()`: Crea un nuevo usuario en la base de datos
- `login()`: Verifica credenciales y devuelve datos del usuario
- `validarPassword()`: Comprueba que la contraseña sea segura
- `emailExiste()`: Verifica si un email ya está registrado
- `registrarIntentoFallido()`: Cuenta intentos de login fallidos

**¿Qué es la lógica de negocio?** Son las reglas del sistema. Por ejemplo: "la contraseña debe tener 8 caracteres", "después de 5 intentos se bloquea la cuenta".

**Para explicar:** "Es el cerebro del sistema. Aquí están todas las reglas: qué se puede hacer, qué no, cómo se hace. Como un reglamento."

---

### 4. Controlador (src/www/controladores/)

#### AuthController.php
Recibe las peticiones del usuario y decide qué hacer:

1. Recoge los datos que envía el formulario
2. Los valida (¿están todos los campos?, ¿el email es válido?)
3. Llama al modelo para procesar la operación
4. Redirige a la vista correspondiente

**Para explicar:** "Es como un recepcionista. Recibe al visitante (la petición), le pregunta qué necesita, gestiona su solicitud con el departamento correspondiente (modelo) y le indica dónde ir después (vista)."

---

### 5. Vistas (src/www/vistas/)

Son las páginas que ve el usuario:

#### login.php
Formulario para iniciar sesión con:
- Campo de email
- Campo de contraseña
- Botón de enviar
- Enlace a registro

#### registro.php
Formulario para crear cuenta con:
- Datos básicos (nombre, email, teléfono, contraseña)
- Selector de rol
- Campos específicos que aparecen según el rol elegido

#### dashboard.php
Página principal tras iniciar sesión. Muestra un mensaje de bienvenida.

**Para explicar:** "Son las pantallas que ve el usuario. Como la fachada y el interior decorado de la casa."

---

### 6. CSS y JavaScript

#### css/styles.css
Todos los estilos visuales:
- Colores
- Tamaños de letra
- Espaciados
- Diseño de botones, formularios, alertas

**Para explicar:** "Es la decoración de la casa: pintura, muebles, cortinas. Hace que todo se vea bonito."

#### js/registro.js
JavaScript que:
- Muestra/oculta campos según el rol seleccionado
- Valida que las contraseñas coincidan

**Para explicar:** "Son las funciones automáticas de la casa: cuando pulsas el interruptor se enciende la luz, cuando abres el grifo sale agua."

---

## Arquitectura MVC

El proyecto sigue el patrón **Modelo-Vista-Controlador**:

```
[Usuario] → [Controlador] → [Modelo] → [Base de Datos]
                ↓
            [Vista]
```

1. El usuario envía un formulario (hace clic en "Iniciar Sesión")
2. El **Controlador** recibe los datos y los valida
3. El **Modelo** ejecuta la lógica (consulta la base de datos)
4. El Controlador decide qué **Vista** mostrar
5. La Vista se muestra al usuario

**Para explicar:** "Es como un restaurante. El cliente (usuario) hace un pedido al camarero (controlador), el camarero lo pasa a la cocina (modelo), la cocina prepara el plato con los ingredientes del almacén (base de datos) y el camarero sirve el plato en un plato bonito (vista)."

---

## Seguridad Implementada

### 1. Contraseñas Hasheadas
Las contraseñas nunca se guardan en texto plano. Se usa `password_hash()` de PHP que aplica el algoritmo BCrypt.

**Para explicar:** "Es como guardar una huella dactilar en lugar del dedo. Aunque alguien robe la base de datos, no puede saber las contraseñas originales."

### 2. Prepared Statements
Todas las consultas SQL usan prepared statements de PDO para evitar inyección SQL.

**Para explicar:** "Es como un formulario oficial donde solo puedes escribir en las casillas. Aunque intentes escribir algo malicioso, el sistema lo trata como texto normal, no como una orden."

### 3. Bloqueo por Intentos Fallidos
Después de 5 intentos incorrectos, la cuenta se bloquea 15 minutos.

**Para explicar:** "Es como cuando metes mal el PIN del móvil varias veces y te bloquea. Evita que alguien pruebe miles de contraseñas."

### 4. Mensajes Genéricos
El mensaje de error siempre dice "Credenciales inválidas", nunca especifica si el email existe o si la contraseña es incorrecta.

**Para explicar:** "Si un ladrón intenta entrar y le dices 'esa llave no es', sabe que la cerradura es la correcta. Mejor decir simplemente 'no puedes entrar'."

---

## Instalación

### Requisitos
- PHP 8.0 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Servidor web (Apache o Nginx)

### Pasos

1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd sistema-fct-sprint1
   ```

2. **Crear la base de datos**
   ```bash
   mysql -u root -p < src/sql/bbdd.sql
   mysql -u root -p < src/sql/datos_iniciales.sql
   mysql -u root -p < src/sql/datos_pruebas.sql
   ```

3. **Configurar la conexión**
   Editar `src/www/includes/config.php` con tus datos:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sistema_fct');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   ```

4. **Configurar el servidor web**
   Apuntar el DocumentRoot a `src/www/`

5. **Acceder al sistema**
   Abrir `http://localhost/sistema-fct` en el navegador

---

## Usuarios de Prueba

| Email | Contraseña | Rol |
|-------|------------|-----|
| coordinador@iescastelar.es | password | Coordinador |
| carlos.martinez@alumno.iescastelar.es | password | Estudiante |
| tutor1@techsolutions.es | password | Tutor Empresa |

*Nota: La contraseña "password" es solo para pruebas. El hash almacenado corresponde a esta contraseña.*

---

## Flujo de Uso

### Registrarse
1. Ir a la página de registro
2. Rellenar nombre, email y contraseña
3. Seleccionar rol (aparecen campos adicionales)
4. Clic en "Crear Cuenta"
5. Si todo es correcto → Redirige al login con mensaje de éxito

### Iniciar Sesión
1. Ir a la página de login
2. Introducir email y contraseña
3. Clic en "Iniciar Sesión"
4. Si es correcto → Redirige al dashboard
5. Si es incorrecto → Muestra error

### Cerrar Sesión
1. Clic en "Cerrar Sesión" en la barra superior
2. Se destruye la sesión
3. Redirige al login

---

## Próximos Sprints

- **Sprint 2**: Fichas de seguimiento diario, validación por tutores
- **Sprint 3**: Control de horas, gestión de incidencias
- **Sprint 4**: Evaluaciones, dashboard con estadísticas


**Alberto Jiménez Hernández**  
IES Castelar - Badajoz  
2º DAW - Curso 2025-2026
