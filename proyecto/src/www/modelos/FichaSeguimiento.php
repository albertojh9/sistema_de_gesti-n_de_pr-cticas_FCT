<?php
/**
 * Modelo FichaSeguimiento
 * Gestiona las operaciones relacionadas con las fichas de seguimiento
 */

require_once __DIR__ . '/../config/Database.php';

class FichaSeguimiento {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Crea una nueva ficha de seguimiento
     * HU-02: Registro de Ficha de Seguimiento Diaria
     */
    public function crear($datos) {
        try {
            // Verificar que no exista ya una ficha para esa asignación y fecha
            if ($this->existeFicha($datos['asignacion_id'], $datos['fecha'])) {
                return ['success' => false, 'message' => 'Ya existe una ficha para esta fecha'];
            }

            $query = "INSERT INTO ficha_seguimiento 
                      (asignacion_id, fecha, hora_entrada, hora_salida, descripcion, 
                       competencias, dificultades, valoracion, estado) 
                      VALUES 
                      (:asignacion_id, :fecha, :hora_entrada, :hora_salida, :descripcion, 
                       :competencias, :dificultades, :valoracion, 'PENDIENTE')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':asignacion_id', $datos['asignacion_id']);
            $stmt->bindParam(':fecha', $datos['fecha']);
            $stmt->bindParam(':hora_entrada', $datos['hora_entrada']);
            $stmt->bindParam(':hora_salida', $datos['hora_salida']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':competencias', $datos['competencias']);
            $stmt->bindParam(':dificultades', $datos['dificultades']);
            $stmt->bindParam(':valoracion', $datos['valoracion']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Ficha creada correctamente',
                    'id' => $this->conn->lastInsertId()
                ];
            }
            
            return ['success' => false, 'message' => 'Error al crear la ficha'];
        } catch (PDOException $e) {
            error_log("Error al crear ficha: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear la ficha'];
        }
    }

    /**
     * Actualiza una ficha de seguimiento existente (solo si está pendiente)
     */
    public function actualizar($id, $datos) {
        try {
            // Verificar que la ficha esté en estado PENDIENTE
            $ficha = $this->obtenerPorId($id);
            if (!$ficha || $ficha['estado'] !== 'PENDIENTE') {
                return ['success' => false, 'message' => 'No se puede editar esta ficha'];
            }

            $query = "UPDATE ficha_seguimiento SET 
                      fecha = :fecha,
                      hora_entrada = :hora_entrada,
                      hora_salida = :hora_salida,
                      descripcion = :descripcion,
                      competencias = :competencias,
                      dificultades = :dificultades,
                      valoracion = :valoracion
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':fecha', $datos['fecha']);
            $stmt->bindParam(':hora_entrada', $datos['hora_entrada']);
            $stmt->bindParam(':hora_salida', $datos['hora_salida']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':competencias', $datos['competencias']);
            $stmt->bindParam(':dificultades', $datos['dificultades']);
            $stmt->bindParam(':valoracion', $datos['valoracion']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Ficha actualizada correctamente'];
            }
            
            return ['success' => false, 'message' => 'Error al actualizar la ficha'];
        } catch (PDOException $e) {
            error_log("Error al actualizar ficha: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar la ficha'];
        }
    }

    /**
     * Valida una ficha de seguimiento
     * HU-03: Validación de Fichas de Seguimiento
     */
    public function validar($id, $tutor_id, $accion, $comentarios = '') {
        try {
            $estado = ($accion === 'aprobar') ? 'VALIDADA' : 'RECHAZADA';
            
            $query = "UPDATE ficha_seguimiento SET 
                      estado = :estado,
                      comentarios_tutor = :comentarios,
                      validada_por = :tutor_id,
                      fecha_validacion = NOW()
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':comentarios', $comentarios);
            $stmt->bindParam(':tutor_id', $tutor_id);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $mensaje = $estado === 'VALIDADA' ? 'Ficha aprobada correctamente' : 'Ficha rechazada';
                return ['success' => true, 'message' => $mensaje];
            }
            
            return ['success' => false, 'message' => 'Error al validar la ficha'];
        } catch (PDOException $e) {
            error_log("Error al validar ficha: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al validar la ficha'];
        }
    }

    /**
     * Obtiene las fichas de una asignación
     */
    public function obtenerPorAsignacion($asignacion_id, $estado = null) {
        try {
            $query = "SELECT fs.*, 
                      DATE_FORMAT(fs.fecha, '%d/%m/%Y') as fecha_formateada,
                      DATE_FORMAT(fs.hora_entrada, '%H:%i') as hora_entrada_formateada,
                      DATE_FORMAT(fs.hora_salida, '%H:%i') as hora_salida_formateada,
                      u.nombre as validada_por_nombre
                      FROM ficha_seguimiento fs
                      LEFT JOIN tutor_empresa te ON fs.validada_por = te.id
                      LEFT JOIN usuario u ON te.usuario_id = u.id
                      WHERE fs.asignacion_id = :asignacion_id";
            
            if ($estado) {
                $query .= " AND fs.estado = :estado";
            }
            
            $query .= " ORDER BY fs.fecha DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':asignacion_id', $asignacion_id);
            
            if ($estado) {
                $stmt->bindParam(':estado', $estado);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener fichas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las fichas pendientes de validación de un tutor
     * HU-03: Validación de Fichas de Seguimiento
     */
    public function obtenerPendientesPorTutor($tutor_id) {
        try {
            $query = "SELECT fs.*,
                      DATE_FORMAT(fs.fecha, '%d/%m/%Y') as fecha_formateada,
                      DATE_FORMAT(fs.hora_entrada, '%H:%i') as hora_entrada_formateada,
                      DATE_FORMAT(fs.hora_salida, '%H:%i') as hora_salida_formateada,
                      e.id as estudiante_id,
                      u.nombre as estudiante_nombre,
                      est.ciclo_formativo
                      FROM ficha_seguimiento fs
                      INNER JOIN asignacion a ON fs.asignacion_id = a.id
                      INNER JOIN estudiante e ON a.estudiante_id = e.id
                      INNER JOIN usuario u ON e.usuario_id = u.id
                      INNER JOIN estudiante est ON e.id = est.id
                      WHERE a.tutor_empresa_id = :tutor_id 
                      AND fs.estado = 'PENDIENTE'
                      ORDER BY fs.fecha DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tutor_id', $tutor_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener fichas pendientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una ficha por su ID
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT fs.*,
                      DATE_FORMAT(fs.fecha, '%d/%m/%Y') as fecha_formateada,
                      u.nombre as validada_por_nombre,
                      e.id as estudiante_id,
                      ue.nombre as estudiante_nombre
                      FROM ficha_seguimiento fs
                      LEFT JOIN tutor_empresa te ON fs.validada_por = te.id
                      LEFT JOIN usuario u ON te.usuario_id = u.id
                      INNER JOIN asignacion a ON fs.asignacion_id = a.id
                      INNER JOIN estudiante e ON a.estudiante_id = e.id
                      INNER JOIN usuario ue ON e.usuario_id = ue.id
                      WHERE fs.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener ficha: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si ya existe una ficha para una asignación y fecha
     */
    private function existeFicha($asignacion_id, $fecha) {
        try {
            $query = "SELECT COUNT(*) as total FROM ficha_seguimiento 
                      WHERE asignacion_id = :asignacion_id AND fecha = :fecha";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':asignacion_id', $asignacion_id);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->execute();
            
            $resultado = $stmt->fetch();
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar ficha: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida los datos de una ficha
     */
    public function validarDatos($datos) {
        $errores = [];

        if (empty($datos['fecha'])) {
            $errores[] = "La fecha es obligatoria";
        }

        if (empty($datos['hora_entrada'])) {
            $errores[] = "La hora de entrada es obligatoria";
        }

        if (empty($datos['hora_salida'])) {
            $errores[] = "La hora de salida es obligatoria";
        }

        if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 50) {
            $errores[] = "La descripción debe tener al menos 50 caracteres";
        }

        if (!empty($datos['valoracion']) && ($datos['valoracion'] < 1 || $datos['valoracion'] > 5)) {
            $errores[] = "La valoración debe estar entre 1 y 5";
        }

        return $errores;
    }
}
