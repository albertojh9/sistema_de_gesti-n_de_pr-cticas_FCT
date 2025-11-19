-- =============================================
-- Sistema de Gestión de Prácticas FCT
-- Datos Iniciales
-- Autor: Alberto Jiménez Hernández
-- =============================================

USE sistema_fct;

-- =============================================
-- Competencias por defecto para DAW
-- =============================================
INSERT INTO Competencia (nombre, categoria, ciclo_formativo, descripcion) VALUES
-- Competencias Técnicas
('Desarrollo Backend', 'Técnica', 'DAW', 'Programación del lado del servidor'),
('Desarrollo Frontend', 'Técnica', 'DAW', 'Diseño e implementación de interfaces'),
('Base de Datos', 'Técnica', 'DAW', 'Gestión y administración de bases de datos'),
('Control de Versiones', 'Técnica', 'DAW', 'Uso de Git y sistemas de control de versiones'),
('Testing', 'Técnica', 'DAW', 'Pruebas y depuración de código'),
('Despliegue', 'Técnica', 'DAW', 'Configuración de servidores y despliegue'),

-- Habilidades Transversales
('Trabajo en Equipo', 'Transversal', 'DAW', 'Colaboración efectiva con compañeros'),
('Comunicación', 'Transversal', 'DAW', 'Capacidad de comunicación oral y escrita'),
('Resolución de Problemas', 'Transversal', 'DAW', 'Análisis y solución de problemas'),
('Aprendizaje Autónomo', 'Transversal', 'DAW', 'Capacidad de aprender de forma independiente'),
('Organización', 'Transversal', 'DAW', 'Planificación y gestión del tiempo'),

-- Actitud
('Puntualidad', 'Actitud', 'DAW', 'Cumplimiento de horarios'),
('Responsabilidad', 'Actitud', 'DAW', 'Compromiso con las tareas asignadas'),
('Iniciativa', 'Actitud', 'DAW', 'Proactividad en el trabajo'),
('Adaptabilidad', 'Actitud', 'DAW', 'Flexibilidad ante cambios');

-- =============================================
-- Usuario Coordinador por defecto
-- Password: Admin123! (hasheada con password_hash)
-- =============================================
INSERT INTO Usuario (email, password, rol, nombre, telefono, activo) VALUES
('coordinador@iescastelar.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COORDINADOR', 'Coordinador FCT', '924123456', TRUE);

INSERT INTO Coordinador (usuario_id, centro_educativo, departamento) VALUES
(1, 'IES Castelar', 'Informática');
