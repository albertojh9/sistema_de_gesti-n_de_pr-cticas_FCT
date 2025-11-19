-- =============================================
-- Sistema de Gestión de Prácticas FCT
-- Base de Datos - Sprint 1
-- Autor: Alberto Jiménez Hernández
-- =============================================

-- Eliminar base de datos si existe y crearla
DROP DATABASE IF EXISTS sistema_fct;
CREATE DATABASE sistema_fct CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE sistema_fct;

-- =============================================
-- TABLA: Usuario
-- Almacena todos los usuarios del sistema
-- =============================================
CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('ESTUDIANTE', 'TUTOR_EMPRESA', 'COORDINADOR') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NULL,
    foto_perfil VARCHAR(255) NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    intentos_fallidos INT DEFAULT 0,
    bloqueado_hasta TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- =============================================
-- TABLA: Estudiante
-- Datos específicos de estudiantes
-- =============================================
CREATE TABLE Estudiante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    dni VARCHAR(20) NOT NULL UNIQUE,
    ciclo_formativo VARCHAR(100) NOT NULL,
    grupo VARCHAR(20) NULL,
    año_academico VARCHAR(10) NOT NULL,
    
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE,
    INDEX idx_dni (dni),
    INDEX idx_ciclo (ciclo_formativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- =============================================
-- TABLA: TutorEmpresa
-- Datos específicos de tutores de empresa
-- =============================================
CREATE TABLE TutorEmpresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    empresa_id INT NULL,
    cargo VARCHAR(100) NULL,
    departamento VARCHAR(100) NULL,
    
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- =============================================
-- TABLA: Coordinador
-- Datos específicos de coordinadores FCT
-- =============================================
CREATE TABLE Coordinador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    centro_educativo VARCHAR(150) NOT NULL,
    departamento VARCHAR(100) NULL,
    
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- =============================================
-- TABLA: Empresa
-- Empresas colaboradoras
-- =============================================
CREATE TABLE Empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cif VARCHAR(20) NOT NULL UNIQUE,
    razon_social VARCHAR(150) NOT NULL,
    sector VARCHAR(100) NULL,
    direccion VARCHAR(255) NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_cif (cif),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- =============================================
-- TABLA: Competencia
-- Competencias evaluables
-- =============================================
CREATE TABLE Competencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    ciclo_formativo VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    
    INDEX idx_ciclo (ciclo_formativo),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Añadir FK de TutorEmpresa a Empresa (después de crear Empresa)
ALTER TABLE TutorEmpresa 
ADD FOREIGN KEY (empresa_id) REFERENCES Empresa(id) ON DELETE SET NULL;
