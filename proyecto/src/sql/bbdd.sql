-- Sistema de Gestión de Prácticas FCT
-- Base de datos principal
-- Autor: Alberto Jiménez Hernández

DROP DATABASE IF EXISTS sistemas_fct;
CREATE DATABASE sistemas_fct CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistemas_fct;

-- Tabla de usuarios (base para todos los roles)
CREATE TABLE usuario (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('ESTUDIANTE', 'TUTOR_EMPRESA', 'COORDINADOR') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NULL,
    foto_perfil VARCHAR(255) NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB;

-- Tabla de estudiantes
CREATE TABLE estudiante (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNIQUE NOT NULL,
    dni VARCHAR(20) UNIQUE NOT NULL,
    ciclo_formativo VARCHAR(100) NOT NULL,
    grupo VARCHAR(20) NULL,
    año_academico VARCHAR(10) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    INDEX idx_dni (dni)
) ENGINE=InnoDB;

-- Tabla de tutores de empresa
CREATE TABLE tutor_empresa (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNIQUE NOT NULL,
    empresa_id BIGINT NULL,
    cargo VARCHAR(100) NULL,
    departamento VARCHAR(100) NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de coordinadores
CREATE TABLE coordinador (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNIQUE NOT NULL,
    centro_educativo VARCHAR(200) NOT NULL,
    departamento VARCHAR(100) NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de empresas
CREATE TABLE empresa (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    cif VARCHAR(20) UNIQUE NOT NULL,
    nombre_comercial VARCHAR(200) NOT NULL,
    razon_social VARCHAR(200) NOT NULL,
    sector VARCHAR(100) NULL,
    direccion TEXT NULL,
    localidad VARCHAR(100) NULL,
    provincia VARCHAR(100) NULL,
    codigo_postal VARCHAR(10) NULL,
    telefono VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    web VARCHAR(255) NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cif (cif)
) ENGINE=InnoDB;

-- Tabla de competencias
CREATE TABLE competencia (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    categoria ENUM('TECNICA', 'TRANSVERSAL', 'ACTITUDINAL') NOT NULL,
    descripcion TEXT NULL,
    ciclo_formativo VARCHAR(100) NULL,
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB;

-- Tabla de asignaciones (estudiante-empresa-tutor)
CREATE TABLE asignacion (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id BIGINT NOT NULL,
    tutor_empresa_id BIGINT NOT NULL,
    empresa_id BIGINT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    horas_requeridas INT NOT NULL,
    horas_realizadas INT DEFAULT 0,
    estado ENUM('ACTIVA', 'FINALIZADA', 'CANCELADA') DEFAULT 'ACTIVA',
    calificacion DECIMAL(4,2) NULL,
    observaciones TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiante(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_empresa_id) REFERENCES tutor_empresa(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES empresa(id) ON DELETE CASCADE,
    INDEX idx_estado (estado),
    INDEX idx_estudiante (estudiante_id)
) ENGINE=InnoDB;

-- Tabla de fichas de seguimiento
CREATE TABLE ficha_seguimiento (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    asignacion_id BIGINT NOT NULL,
    fecha DATE NOT NULL,
    hora_entrada TIME NOT NULL,
    hora_salida TIME NOT NULL,
    horas_dia DECIMAL(4,2) GENERATED ALWAYS AS (
        TIMESTAMPDIFF(MINUTE, 
            TIMESTAMP(fecha, hora_entrada), 
            TIMESTAMP(fecha, hora_salida)
        ) / 60.0
    ) STORED,
    descripcion TEXT NOT NULL,
    competencias VARCHAR(500) NULL COMMENT 'IDs de competencias separados por comas',
    dificultades TEXT NULL,
    valoracion INT NULL CHECK (valoracion >= 1 AND valoracion <= 5),
    estado ENUM('PENDIENTE', 'VALIDADA', 'RECHAZADA') DEFAULT 'PENDIENTE',
    comentarios_tutor TEXT NULL,
    validada_por BIGINT NULL,
    fecha_validacion TIMESTAMP NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asignacion_id) REFERENCES asignacion(id) ON DELETE CASCADE,
    FOREIGN KEY (validada_por) REFERENCES tutor_empresa(id) ON DELETE SET NULL,
    UNIQUE KEY unique_asignacion_fecha (asignacion_id, fecha),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB;

-- Tabla de evaluaciones
CREATE TABLE evaluacion (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    asignacion_id BIGINT NOT NULL,
    evaluador_id BIGINT NOT NULL,
    tipo ENUM('PARCIAL', 'FINAL') NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT NULL,
    estado ENUM('BORRADOR', 'ENVIADA') DEFAULT 'BORRADOR',
    FOREIGN KEY (asignacion_id) REFERENCES asignacion(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluador_id) REFERENCES tutor_empresa(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de competencias evaluadas
CREATE TABLE competencia_evaluada (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    evaluacion_id BIGINT NOT NULL,
    competencia_id BIGINT NOT NULL,
    valoracion ENUM('NO_OBSERVADO', 'EN_DESARROLLO', 'LOGRADO', 'DESTACADO') NOT NULL,
    comentarios TEXT NULL,
    FOREIGN KEY (evaluacion_id) REFERENCES evaluacion(id) ON DELETE CASCADE,
    FOREIGN KEY (competencia_id) REFERENCES competencia(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de incidencias
CREATE TABLE incidencia (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    asignacion_id BIGINT NOT NULL,
    reportada_por BIGINT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    urgencia ENUM('BAJA', 'MEDIA', 'ALTA') DEFAULT 'MEDIA',
    estado ENUM('ABIERTA', 'EN_PROCESO', 'RESUELTA') DEFAULT 'ABIERTA',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion TIMESTAMP NULL,
    solucion TEXT NULL,
    FOREIGN KEY (asignacion_id) REFERENCES asignacion(id) ON DELETE CASCADE,
    FOREIGN KEY (reportada_por) REFERENCES usuario(id) ON DELETE CASCADE,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla de mensajes
CREATE TABLE mensaje (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    emisor_id BIGINT NOT NULL,
    receptor_id BIGINT NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisor_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (receptor_id) REFERENCES usuario(id) ON DELETE CASCADE,
    INDEX idx_receptor (receptor_id),
    INDEX idx_leido (leido)
) ENGINE=InnoDB;

-- Trigger para actualizar horas_realizadas en asignacion al validar fichas
DELIMITER //
CREATE TRIGGER actualizar_horas_realizadas 
AFTER UPDATE ON ficha_seguimiento
FOR EACH ROW
BEGIN
    IF NEW.estado = 'VALIDADA' AND OLD.estado != 'VALIDADA' THEN
        UPDATE asignacion 
        SET horas_realizadas = (
            SELECT COALESCE(SUM(horas_dia), 0)
            FROM ficha_seguimiento
            WHERE asignacion_id = NEW.asignacion_id 
            AND estado = 'VALIDADA'
        )
        WHERE id = NEW.asignacion_id;
    END IF;
END//
DELIMITER ;
