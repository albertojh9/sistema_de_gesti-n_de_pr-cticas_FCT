<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Modelo Usuario
 * 
 * @author Alberto Jiménez Hernández
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/config.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Registrar nuevo usuario
     * 
     * @param array $datos Datos del usuario
     * @return array Resultado con éxito/error
     */
    public function registrar($datos) {
        try {
            // Verificar si el email ya existe
            if ($this->emailExiste($datos['email'])) {
                return ['exito' => false, 'error' => 'El correo electrónico ya está registrado'];
            }
            
            // Validar contraseña
            $validacionPassword = $this->validarPassword($datos['password']);
            if (!$validacionPassword['valida']) {
                return ['exito' => false, 'error' => $validacionPassword['mensaje']];
            }
            
            // Hashear contraseña
            $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // Insertar usuario
            $sql = "INSERT INTO Usuario (email, password, rol, nombre, telefono, activo) 
                    VALUES (:email, :password, :rol, :nombre, :telefono, TRUE)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $datos['email'],
                ':password' => $passwordHash,
                ':rol' => $datos['rol'],
                ':nombre' => $datos['nombre'],
                ':telefono' => $datos['telefono'] ?? null
            ]);
            
            $usuarioId = $this->db->lastInsertId();
            
            // Insertar datos específicos según rol
            switch ($datos['rol']) {
                case 'ESTUDIANTE':
                    $this->insertarEstudiante($usuarioId, $datos);
                    break;
                case 'TUTOR_EMPRESA':
                    $this->insertarTutorEmpresa($usuarioId, $datos);
                    break;
                case 'COORDINADOR':
                    $this->insertarCoordinador($usuarioId, $datos);
                    break;
            }
            
            $this->db->commit();
            
            return ['exito' => true, 'usuario_id' => $usuarioId];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            return ['exito' => false, 'error' => 'Error al registrar usuario: ' . $e->getMessage()];
        }
    }
    
    /**
     * Iniciar sesión
     * 
     * @param string $email
     * @param string $password
     * @return array Resultado con datos del usuario o error
     */
    public function login($email, $password) {
        try {
            // Obtener usuario
            $sql = "SELECT * FROM Usuario WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();
            
            // Verificar si existe
            if (!$usuario) {
                return ['exito' => false, 'error' => 'Credenciales inválidas'];
            }
            
            // Verificar si está activo
            if (!$usuario['activo']) {
                return ['exito' => false, 'error' => 'Tu cuenta está desactivada. Contacta con el coordinador.'];
            }
            
            // Verificar bloqueo temporal
            if ($usuario['bloqueado_hasta'] && strtotime($usuario['bloqueado_hasta']) > time()) {
                $tiempoRestante = ceil((strtotime($usuario['bloqueado_hasta']) - time()) / 60);
                return ['exito' => false, 'error' => "Cuenta bloqueada. Intenta de nuevo en $tiempoRestante minutos."];
            }
            
            // Verificar contraseña
            if (!password_verify($password, $usuario['password'])) {
                $this->registrarIntentoFallido($usuario['id']);
                return ['exito' => false, 'error' => 'Credenciales inválidas'];
            }
            
            // Resetear intentos fallidos y actualizar último acceso
            $this->resetearIntentosFallidos($usuario['id']);
            $this->actualizarUltimoAcceso($usuario['id']);
            
            // Eliminar contraseña del array antes de devolver
            unset($usuario['password']);
            
            return ['exito' => true, 'usuario' => $usuario];
            
        } catch (PDOException $e) {
            return ['exito' => false, 'error' => 'Error al iniciar sesión'];
        }
    }
    
    /**
     * Verificar si un email ya existe
     */
    private function emailExiste($email) {
        $sql = "SELECT COUNT(*) FROM Usuario WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Validar requisitos de contraseña
     */
    private function validarPassword($password) {
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return ['valida' => false, 'mensaje' => 'La contraseña debe tener al menos ' . PASSWORD_MIN_LENGTH . ' caracteres'];
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return ['valida' => false, 'mensaje' => 'La contraseña debe contener al menos una mayúscula'];
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return ['valida' => false, 'mensaje' => 'La contraseña debe contener al menos una minúscula'];
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return ['valida' => false, 'mensaje' => 'La contraseña debe contener al menos un número'];
        }
        
        return ['valida' => true];
    }
    
    /**
     * Insertar datos específicos de estudiante
     */
    private function insertarEstudiante($usuarioId, $datos) {
        $sql = "INSERT INTO Estudiante (usuario_id, dni, ciclo_formativo, grupo, año_academico) 
                VALUES (:usuario_id, :dni, :ciclo_formativo, :grupo, :año_academico)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':dni' => $datos['dni'],
            ':ciclo_formativo' => $datos['ciclo_formativo'],
            ':grupo' => $datos['grupo'] ?? null,
            ':año_academico' => $datos['año_academico']
        ]);
    }
    
    /**
     * Insertar datos específicos de tutor de empresa
     */
    private function insertarTutorEmpresa($usuarioId, $datos) {
        $sql = "INSERT INTO TutorEmpresa (usuario_id, empresa_id, cargo, departamento) 
                VALUES (:usuario_id, :empresa_id, :cargo, :departamento)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':empresa_id' => $datos['empresa_id'] ?? null,
            ':cargo' => $datos['cargo'] ?? null,
            ':departamento' => $datos['departamento'] ?? null
        ]);
    }
    
    /**
     * Insertar datos específicos de coordinador
     */
    private function insertarCoordinador($usuarioId, $datos) {
        $sql = "INSERT INTO Coordinador (usuario_id, centro_educativo, departamento) 
                VALUES (:usuario_id, :centro_educativo, :departamento)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':centro_educativo' => $datos['centro_educativo'],
            ':departamento' => $datos['departamento'] ?? null
        ]);
    }
    
    /**
     * Registrar intento de login fallido
     */
    private function registrarIntentoFallido($usuarioId) {
        $sql = "UPDATE Usuario SET intentos_fallidos = intentos_fallidos + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        
        // Bloquear si supera el máximo de intentos
        $sql = "SELECT intentos_fallidos FROM Usuario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        $intentos = $stmt->fetchColumn();
        
        if ($intentos >= MAX_LOGIN_ATTEMPTS) {
            $bloqueoHasta = date('Y-m-d H:i:s', time() + LOCKOUT_TIME);
            $sql = "UPDATE Usuario SET bloqueado_hasta = :bloqueado_hasta WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':bloqueado_hasta' => $bloqueoHasta, ':id' => $usuarioId]);
        }
    }
    
    /**
     * Resetear intentos fallidos
     */
    private function resetearIntentosFallidos($usuarioId) {
        $sql = "UPDATE Usuario SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
    }
    
    /**
     * Actualizar último acceso
     */
    private function actualizarUltimoAcceso($usuarioId) {
        $sql = "UPDATE Usuario SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM Usuario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            unset($usuario['password']);
        }
        
        return $usuario;
    }
    
    /**
     * Obtener todas las empresas activas (para formulario de registro)
     */
    public function obtenerEmpresas() {
        $sql = "SELECT id, razon_social FROM Empresa WHERE activa = TRUE ORDER BY razon_social";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
?>
