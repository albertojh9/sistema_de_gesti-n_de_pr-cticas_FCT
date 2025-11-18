<?php
/**
 * ============================================================================
 * CLASE DATABASE - CONEXIÓN A LA BASE DE DATOS
 * ============================================================================
 * 
 * Esta clase implementa el patrón SINGLETON para la conexión a MySQL.
 * 
 * ¿Qué es el patrón Singleton?
 * - Garantiza que solo exista UNA instancia de la clase en toda la aplicación
 * - Evita crear múltiples conexiones a la base de datos (costoso en recursos)
 * - Proporciona un punto de acceso global a esa única instancia
 * 
 * ¿Por qué usamos PDO?
 * - PDO (PHP Data Objects) es una capa de abstracción para bases de datos
 * - Permite cambiar de MySQL a PostgreSQL, SQLite, etc. sin cambiar el código
 * - Soporta prepared statements que previenen inyección SQL
 * - Es más seguro y moderno que mysqli o mysql_*
 */

// Cargar el archivo de configuración que contiene las constantes de conexión
require_once __DIR__ . '/config.php';

class Database {
    
    /**
     * $instance: Almacena la única instancia de la clase (Singleton)
     * - Es static porque pertenece a la clase, no a una instancia
     * - Es private para que solo la clase pueda acceder
     * - Inicialmente es null hasta que se cree la primera instancia
     */
    private static $instance = null;
    
    /**
     * $connection: Almacena el objeto PDO de conexión
     * - Es private para encapsular la conexión
     * - Solo se accede a través del método getConnection()
     */
    private $connection;

    /**
     * ========================================================================
     * CONSTRUCTOR PRIVADO
     * ========================================================================
     * 
     * El constructor es PRIVADO para impedir crear instancias con "new Database()"
     * Esto es fundamental para el patrón Singleton.
     * Solo se puede obtener la instancia mediante getInstance()
     */
    private function __construct() {
        try {
            /**
             * DSN (Data Source Name): Cadena de conexión
             * Formato: "driver:host=servidor;dbname=basedatos;charset=codificacion"
             * 
             * Usamos las constantes definidas en config.php:
             * - DB_HOST: localhost
             * - DB_NAME: sistemas_fct
             * - DB_CHARSET: utf8mb4
             */
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            /**
             * Opciones de PDO para configurar el comportamiento
             */
            $options = [
                /**
                 * ERRMODE_EXCEPTION: Lanza excepciones cuando hay errores
                 * - Permite usar try-catch para manejar errores
                 * - Es más seguro que mostrar errores en pantalla
                 */
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                
                /**
                 * FETCH_ASSOC: Devuelve arrays asociativos por defecto
                 * - $row['nombre'] en lugar de $row[0]
                 * - Código más legible y mantenible
                 */
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                /**
                 * EMULATE_PREPARES = false: Usa prepared statements nativos
                 * - Más seguro contra inyección SQL
                 * - El servidor MySQL prepara la consulta
                 */
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            /**
             * Crear la conexión PDO
             * Parámetros: DSN, usuario, contraseña, opciones
             */
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            /**
             * Si hay error de conexión, mostrar mensaje y terminar
             * die() detiene la ejecución del script
             * 
             * En producción, deberías:
             * - Registrar el error en un log
             * - Mostrar un mensaje genérico al usuario
             * - NO mostrar detalles técnicos
             */
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * ========================================================================
     * getInstance(): Obtener la instancia única (Singleton)
     * ========================================================================
     * 
     * Este método es el ÚNICO punto de acceso a la instancia de Database.
     * 
     * Funcionamiento:
     * 1. Si no existe instancia ($instance === null), la crea
     * 2. Si ya existe, devuelve la existente
     * 
     * Uso: $db = Database::getInstance();
     * 
     * @return Database La única instancia de la clase
     */
    public static function getInstance() {
        // Si la instancia no existe, crearla
        if (self::$instance === null) {
            // self:: se usa para acceder a miembros estáticos de la misma clase
            self::$instance = new self();  // new self() es equivalente a new Database()
        }
        // Devolver la instancia (nueva o existente)
        return self::$instance;
    }

    /**
     * ========================================================================
     * getConnection(): Obtener el objeto PDO
     * ========================================================================
     * 
     * Devuelve la conexión PDO para poder hacer consultas.
     * 
     * Uso típico:
     * $db = Database::getInstance();
     * $conn = $db->getConnection();
     * $stmt = $conn->prepare("SELECT * FROM usuario");
     * 
     * @return PDO Objeto de conexión a la base de datos
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * ========================================================================
     * __clone(): Prevenir clonación
     * ========================================================================
     * 
     * Método privado para impedir clonar la instancia.
     * Si alguien intenta: $db2 = clone $db;
     * PHP lanzará un error.
     * 
     * Esto mantiene el patrón Singleton intacto.
     */
    private function __clone() {}

    /**
     * ========================================================================
     * __wakeup(): Prevenir deserialización
     * ========================================================================
     * 
     * Impide recrear la instancia desde una serialización.
     * serialize() convierte un objeto a string para guardarlo
     * unserialize() lo reconstruye
     * 
     * Si alguien intenta deserializar, lanzamos excepción.
     * Esto evita crear múltiples instancias por serialización.
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}
