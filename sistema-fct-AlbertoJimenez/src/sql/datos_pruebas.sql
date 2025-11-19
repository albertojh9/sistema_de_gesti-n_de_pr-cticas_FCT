-- =============================================
-- Sistema de Gestión de Prácticas FCT
-- Datos de Prueba
-- Autor: Alberto Jiménez Hernández
-- =============================================

USE sistema_fct;

-- =============================================
-- Empresas de prueba
-- =============================================
INSERT INTO Empresa (cif, razon_social, sector, direccion, activa) VALUES
('B06123456', 'TechSolutions S.L.', 'Tecnología', 'Calle Mayor 15, Badajoz', TRUE),
('A06654321', 'WebDev Extremadura', 'Desarrollo Web', 'Av. de Elvas 25, Badajoz', TRUE),
('B06789012', 'DataCenter Sur', 'Sistemas', 'Polígono El Nevero, Badajoz', TRUE);

-- =============================================
-- Usuarios de prueba
-- Todas las contraseñas son: Test1234!
-- =============================================

-- Estudiantes
INSERT INTO Usuario (email, password, rol, nombre, telefono, activo) VALUES
('carlos.martinez@alumno.iescastelar.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ESTUDIANTE', 'Carlos Martínez López', '666111222', TRUE),
('ana.garcia@alumno.iescastelar.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ESTUDIANTE', 'Ana García Pérez', '666333444', TRUE),
('luis.perez@alumno.iescastelar.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ESTUDIANTE', 'Luis Pérez Sánchez', '666555666', TRUE);

INSERT INTO Estudiante (usuario_id, dni, ciclo_formativo, grupo, año_academico) VALUES
(2, '12345678A', 'Desarrollo de Aplicaciones Web', '2º DAW', '2024-2025'),
(3, '23456789B', 'Desarrollo de Aplicaciones Web', '2º DAW', '2024-2025'),
(4, '34567890C', 'Desarrollo de Aplicaciones Web', '2º DAW', '2024-2025');

-- Tutores de empresa
INSERT INTO Usuario (email, password, rol, nombre, telefono, activo) VALUES
('tutor1@techsolutions.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR_EMPRESA', 'María Fernández Ruiz', '924111222', TRUE),
('tutor2@webdev.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TUTOR_EMPRESA', 'Pedro Gómez Torres', '924333444', TRUE);

INSERT INTO TutorEmpresa (usuario_id, empresa_id, cargo, departamento) VALUES
(5, 1, 'Jefe de Desarrollo', 'Desarrollo'),
(6, 2, 'CTO', 'Tecnología');
