<?php
/**
 * Modelo Asignacion
 * Gestiona las asignaciones de estudiantes a empresas
 */

require_once __DIR__ . '/../config/Database.php';

class Asignacion {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Obtiene la asignación activa de un estudiante
     */
    public function obtenerAsignacionActivaEstudiante($estudiante_id) {
        try {
            $query = "SELECT a.*, 
                      em.nombre_comercial as empresa_nombre,
                      u.nombre as tutor_nombre,
                      u.email as tutor_email,
                      u.telefono as tutor_telefono
                      FROM asignacion a
                      INNER JOIN empresa em ON a.empresa_id = em.id
                      INNER JOIN tutor_empresa te ON a.tutor_empresa_id = te.id
                      INNER JOIN usuario u ON te.usuario_id = u.id
                      WHERE a.estudiante_id = :estudiante_id 
                      AND a.estado = 'ACTIVA'
                      ORDER BY a.fecha_inicio DESC
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estudiante_id', $estudiante_id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener asignación: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene todas las asignaciones activas de un tutor de empresa
     * HU-03: Para que el tutor vea sus estudiantes asignados
     */
    public function obtenerAsignacionesPorTutor($tutor_id) {
        try {
            $query = "SELECT a.*,
                      e.id as estudiante_id,
                      u.nombre as estudiante_nombre,
                      u.foto_perfil as estudiante_foto,
                      est.ciclo_formativo,
                      est.dni,
                      em.nombre_comercial as empresa_nombre,
                      DATE_FORMAT(a.fecha_inicio, '%d/%m/%Y') as fecha_inicio_formateada,
                      DATE_FORMAT(a.fecha_fin, '%d/%m/%Y') as fecha_fin_formateada,
                      ROUND((a.horas_realizadas / a.horas_requeridas) * 100, 0) as porcentaje_progreso,
                      (SELECT COUNT(*) FROM ficha_seguimiento fs 
                       WHERE fs.asignacion_id = a.id AND fs.estado = 'PENDIENTE') as fichas_pendientes
                      FROM asignacion a
                      INNER JOIN estudiante e ON a.estudiante_id = e.id
                      INNER JOIN usuario u ON e.usuario_id = u.id
                      INNER JOIN estudiante est ON e.id = est.id
                      INNER JOIN empresa em ON a.empresa_id = em.id
                      WHERE a.tutor_empresa_id = :tutor_id 
                      AND a.estado = 'ACTIVA'
                      ORDER BY u.nombre ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tutor_id', $tutor_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener asignaciones del tutor: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el detalle completo de una asignación
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT a.*,
                      e.id as estudiante_id,
                      u.nombre as estudiante_nombre,
                      u.email as estudiante_email,
                      u.telefono as estudiante_telefono,
                      est.dni,
                      est.ciclo_formativo,
                      est.grupo,
                      em.nombre_comercial as empresa_nombre,
                      em.direccion as empresa_direccion,
                      em.telefono as empresa_telefono,
                      ut.nombre as tutor_nombre,
                      DATE_FORMAT(a.fecha_inicio, '%d/%m/%Y') as fecha_inicio_formateada,
                      DATE_FORMAT(a.fecha_fin, '%d/%m/%Y') as fecha_fin_formateada,
                      ROUND((a.horas_realizadas / a.horas_requeridas) * 100, 0) as porcentaje_progreso
                      FROM asignacion a
                      INNER JOIN estudiante e ON a.estudiante_id = e.id
                      INNER JOIN usuario u ON e.usuario_id = u.id
                      INNER JOIN estudiante est ON e.id = est.id
                      INNER JOIN empresa em ON a.empresa_id = em.id
                      INNER JOIN tutor_empresa te ON a.tutor_empresa_id = te.id
                      INNER JOIN usuario ut ON te.usuario_id = ut.id
                      WHERE a.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener asignación: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si una asignación pertenece a un tutor
     */
    public function perteneceATutor($asignacion_id, $tutor_id) {
        try {
            $query = "SELECT COUNT(*) as total FROM asignacion 
                      WHERE id = :asignacion_id AND tutor_empresa_id = :tutor_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':asignacion_id', $asignacion_id);
            $stmt->bindParam(':tutor_id', $tutor_id);
            $stmt->execute();
            
            $resultado = $stmt->fetch();
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar asignación: " . $e->getMessage());
            return false;
        }
    }
}
