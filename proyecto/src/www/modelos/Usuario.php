<?php
/**
 * ============================================================================
 * MODELO USUARIO
 * ============================================================================
 * 
 * Este archivo implementa el MODELO del patrón MVC (Modelo-Vista-Controlador).
 * 
 * ¿Qué hace un Modelo?
 * - Gestiona los DATOS y la LÓGICA DE NEGOCIO
 * - Se comunica con la base de datos
 * - NO se preocupa de cómo se muestran los datos (eso es la Vista)
 * - NO maneja peticiones HTTP (eso es el Controlador)
 * 
 * Este modelo específico gestiona:
 * - Autenticación de usuarios (login)
 * - Validación de credenciales
 * - Consultas a la tabla 'usuario'
 */

// Cargar la clase Database para poder conectar con MySQL
require_once __DIR__ . '/../config/Database.php';

class Usuario {
    
    /**
     * $db: Instancia de la clase Database (Singleton)
     * $conn: Objeto PDO para ejecutar consultas
     * 
     * Son private porque solo esta clase los necesita
     */
    private $db;
    private $conn;

    /**
     * ========================================================================
     * CONSTRUCTOR
     * ========================================================================
     * 
     * Se ejecuta automáticamente cuando se crea un objeto Usuario.
     * Ejemplo: $usuario = new Usuario();
     * 
     * Obtiene la conexión a la base de datos usando el Singleton.
     */
    public function __construct() {
        // Obtener la instancia única de Database
        $this->db = Database::getInstance();
        // Obtener el objeto PDO de conexión
        $this->conn = $this->db->getConnection();
    }

    /**
     * ========================================================================
     * autenticar(): Verificar credenciales de login
     * ========================================================================
     * 
     * Historia de Usuario HU-01: Autenticación de Usuarios
     * 
     * Este método:
     * 1. Busca el usuario por email en la base de datos
     * 2. Verifica que la contraseña sea correcta
     * 3. Devuelve los datos del usuario si todo es correcto
     * 
     * @param string $email    Email del usuario
     * @param string $password Contraseña en texto plano
     * @return array|false     Datos del usuario o false si falla
     */
    public function autenticar($email, $password) {
        try {
            /**
             * Consulta SQL con JOINs para obtener todos los datos del usuario
             * 
             * LEFT JOIN: Une tablas aunque no haya coincidencia
             * - Si el usuario es ESTUDIANTE, tendrá datos en tabla 'estudiante'
             * - Si es TUTOR_EMPRESA, tendrá datos en 'tutor_empresa'
             * - Si es COORDINADOR, tendrá datos en 'coordinador'
             * 
             * CASE: Estructura condicional en SQL (similar a switch en PHP)
             * - Devuelve diferentes valores según el rol del usuario
             */
            $query = "SELECT u.*, 
                      CASE 
                          WHEN u.rol = 'ESTUDIANTE' THEN e.id
                          WHEN u.rol = 'TUTOR_EMPRESA' THEN te.id
                          WHEN u.rol = 'COORDINADOR' THEN c.id
                      END as perfil_id,
                      CASE 
                          WHEN u.rol = 'ESTUDIANTE' THEN e.dni
                          ELSE NULL
                      END as dni,
                      CASE 
                          WHEN u.rol = 'ESTUDIANTE' THEN e.ciclo_formativo
                          ELSE NULL
                      END as ciclo_formativo
                      FROM usuario u
                      LEFT JOIN estudiante e ON u.id = e.usuario_id
                      LEFT JOIN tutor_empresa te ON u.id = te.usuario_id
                      LEFT JOIN coordinador c ON u.id = c.usuario_id
                      WHERE u.email = :email AND u.activo = 1";
            
            /**
             * Prepared Statement (Consulta Preparada)
             * 
             * ¿Por qué usar prepare() en lugar de query()?
             * - SEGURIDAD: Previene inyección SQL
             * - :email es un placeholder que se reemplaza de forma segura
             * - El valor NUNCA se interpreta como código SQL
             * 
             * Ejemplo de inyección SQL que esto previene:
             * Email malicioso: "admin@test.com' OR '1'='1"
             * Con query() esto podría dar acceso no autorizado
             * Con prepare() se trata como texto literal
             */
            $stmt = $this->conn->prepare($query);
            
            /**
             * bindParam(): Asigna el valor al placeholder
             * - Vincula la variable $email al placeholder :email
             * - El valor se escapa automáticamente
             */
            $stmt->bindParam(':email', $email);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            /**
             * fetch(): Obtiene UN registro del resultado
             * - Devuelve array asociativo (por la config FETCH_ASSOC)
             * - Devuelve false si no hay resultados
             */
            $usuario = $stmt->fetch();
            
            /**
             * Verificar contraseña
             * 
             * password_verify(): Compara contraseña con hash
             * - $password: Contraseña en texto plano que introdujo el usuario
             * - $usuario['password']: Hash bcrypt guardado en la BD
             * 
             * ¿Cómo funciona bcrypt?
             * - Es un algoritmo de hash unidireccional
             * - El mismo texto genera diferentes hashes (por el salt)
             * - password_verify() sabe cómo comparar correctamente
             * - NUNCA se puede recuperar la contraseña original del hash
             */
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Contraseña correcta - actualizar último acceso
                $this->actualizarUltimoAcceso($usuario['id']);
                
                /**
                 * unset(): Eliminar la contraseña del array
                 * - Por seguridad, nunca enviamos el hash al cliente
                 * - Aunque es un hash, es información sensible
                 */
                unset($usuario['password']);
                
                // Devolver datos del usuario (sin contraseña)
                return $usuario;
            }
            
            // Usuario no encontrado o contraseña incorrecta
            return false;
            
        } catch (PDOException $e) {
            /**
             * Manejo de errores
             * - error_log(): Guarda el error en el log del servidor
             * - NO mostramos el error al usuario (seguridad)
             * - Devolvemos false para indicar fallo
             */
            error_log("Error en autenticación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ========================================================================
     * actualizarUltimoAcceso(): Registrar cuándo entró el usuario
     * ========================================================================
     * 
     * Actualiza el campo 'ultimo_acceso' con la fecha/hora actual.
     * Útil para:
     * - Saber cuándo fue el último login
     * - Detectar cuentas inactivas
     * - Auditoría de seguridad
     * 
     * @param int $usuario_id ID del usuario
     */
    private function actualizarUltimoAcceso($usuario_id) {
        try {
            /**
             * NOW(): Función SQL que devuelve fecha/hora actual
             * Formato: '2025-01-15 10:30:45'
             */
            $query = "UPDATE usuario SET ultimo_acceso = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
        } catch (PDOException $e) {
            // Solo registrar error, no interrumpir el login
            error_log("Error al actualizar último acceso: " . $e->getMessage());
        }
    }

    /**
     * ========================================================================
     * obtenerPorId(): Buscar usuario por su ID
     * ========================================================================
     * 
     * Obtiene todos los datos de un usuario específico.
     * Útil para mostrar perfil, verificar permisos, etc.
     * 
     * @param int $id ID del usuario
     * @return array|false Datos del usuario o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            // Consulta similar a autenticar() pero busca por ID
            $query = "SELECT u.*, 
                      CASE 
                          WHEN u.rol = 'ESTUDIANTE' THEN e.id
                          WHEN u.rol = 'TUTOR_EMPRESA' THEN te.id
                          WHEN u.rol = 'COORDINADOR' THEN c.id
                      END as perfil_id
                      FROM usuario u
                      LEFT JOIN estudiante e ON u.id = e.usuario_id
                      LEFT JOIN tutor_empresa te ON u.id = te.usuario_id
                      LEFT JOIN coordinador c ON u.id = c.usuario_id
                      WHERE u.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $usuario = $stmt->fetch();
            
            if ($usuario) {
                // Eliminar contraseña por seguridad
                unset($usuario['password']);
            }
            
            return $usuario;
            
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ========================================================================
     * validarEmail(): Verificar formato de email
     * ========================================================================
     * 
     * Comprueba que el email tenga formato válido.
     * 
     * filter_var() con FILTER_VALIDATE_EMAIL verifica:
     * - Contiene @
     * - Tiene dominio después del @
     * - No tiene caracteres inválidos
     * 
     * @param string $email Email a validar
     * @return bool true si es válido, false si no
     */
    public function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * ========================================================================
     * validarPassword(): Verificar longitud de contraseña
     * ========================================================================
     * 
     * Comprueba que la contraseña tenga al menos 8 caracteres.
     * 
     * Esta es una validación básica. En producción podrías añadir:
     * - Al menos una mayúscula
     * - Al menos un número
     * - Al menos un carácter especial
     * 
     * @param string $password Contraseña a validar
     * @return bool true si cumple requisitos, false si no
     */
    public function validarPassword($password) {
        return strlen($password) >= 8;
    }

    /**
     * ========================================================================
     * registrarIntentoFallido(): Registrar login fallido
     * ========================================================================
     * 
     * Registra en el log cuando alguien falla el login.
     * 
     * Esto es útil para:
     * - Detectar ataques de fuerza bruta
     * - Bloquear IPs sospechosas
     * - Auditoría de seguridad
     * 
     * TODO: Implementar bloqueo después de 5 intentos fallidos
     * 
     * @param string $email Email que intentó acceder
     */
    public function registrarIntentoFallido($email) {
        // Por ahora solo registramos en el log
        // En una versión más avanzada:
        // - Guardaríamos en BD con timestamp e IP
        // - Bloquearíamos después de X intentos
        // - Enviaríamos alerta al administrador
        error_log("Intento fallido de login para: " . $email);
    }
}
