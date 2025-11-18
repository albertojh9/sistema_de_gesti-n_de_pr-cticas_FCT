<?php
/**
 * Modelo Competencia
 * Gestiona las competencias del sistema
 */

require_once __DIR__ . '/../config/Database.php';

class Competencia {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Obtiene todas las competencias
     */
    public function obtenerTodas($ciclo_formativo = null) {
        try {
            $query = "SELECT * FROM competencia";
            
            if ($ciclo_formativo) {
                $query .= " WHERE ciclo_formativo = :ciclo_formativo";
            }
            
            $query .= " ORDER BY categoria, nombre";
            
            $stmt = $this->conn->prepare($query);
            
            if ($ciclo_formativo) {
                $stmt->bindParam(':ciclo_formativo', $ciclo_formativo);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener competencias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene competencias agrupadas por categoría
     */
    public function obtenerPorCategoria($ciclo_formativo = null) {
        try {
            $competencias = $this->obtenerTodas($ciclo_formativo);
            
            $agrupadas = [
                'TECNICA' => [],
                'TRANSVERSAL' => [],
                'ACTITUDINAL' => []
            ];
            
            foreach ($competencias as $comp) {
                $agrupadas[$comp['categoria']][] = $comp;
            }
            
            return $agrupadas;
        } catch (Exception $e) {
            error_log("Error al agrupar competencias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una competencia por su ID
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM competencia WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener competencia: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene múltiples competencias por sus IDs
     */
    public function obtenerPorIds($ids) {
        try {
            if (empty($ids)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $query = "SELECT * FROM competencia WHERE id IN ($placeholders)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($ids);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener competencias: " . $e->getMessage());
            return [];
        }
    }
}
