-- Datos iniciales del sistema FCT
-- Contraseñas: todas son "password123" (encriptadas con bcrypt)
-- IMPORTANTE: Ejecutar DESPUÉS de bbdd.sql

USE sistemas_fct;

-- Desactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 0;

-- Insertar usuarios (contraseña: password123)
INSERT INTO usuario (id, email, password, rol, nombre, telefono, activo) VALUES
(1, 'carlos.martinez@ejemplo.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'ESTUDIANTE', 'Carlos Martínez López', '666111222', TRUE),
(2, 'ana.garcia@ejemplo.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'ESTUDIANTE', 'Ana García Pérez', '666333444', TRUE),
(3, 'luis.perez@ejemplo.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'ESTUDIANTE', 'Luis Pérez Sánchez', '666555666', TRUE),
(4, 'tutor.empresa1@techcorp.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'TUTOR_EMPRESA', 'María Rodríguez Fernández', '924123456', TRUE),
(5, 'tutor.empresa2@innovasoft.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'TUTOR_EMPRESA', 'Juan González Martín', '924234567', TRUE),
(6, 'coordinador@iescastelar.es', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'COORDINADOR', 'Miguel Jaque Barbero', '924345678', TRUE);

-- Insertar estudiantes
INSERT INTO estudiante (id, usuario_id, dni, ciclo_formativo, grupo, año_academico) VALUES
(1, 1, '12345678A', 'Desarrollo de Aplicaciones Web', '2º DAW', '2024-2025'),
(2, 2, '23456789B', 'Desarrollo de Aplicaciones Web', '2º DAW', '2024-2025'),
(3, 3, '34567890C', 'Desarrollo de Aplicaciones Web', '2º DAW', '2024-2025');

-- Insertar empresas
INSERT INTO empresa (id, cif, nombre_comercial, razon_social, sector, direccion, localidad, provincia, codigo_postal, telefono, email, activa) VALUES
(1, 'A12345678', 'TechCorp Solutions', 'TechCorp Solutions S.L.', 'Tecnología', 'Calle Innovación 15', 'Badajoz', 'Badajoz', '06001', '924123456', 'info@techcorp.com', TRUE),
(2, 'B23456789', 'InnovaSoft', 'InnovaSoft Desarrollo S.L.', 'Desarrollo Software', 'Avenida Digital 23', 'Mérida', 'Badajoz', '06800', '924234567', 'contacto@innovasoft.com', TRUE);

-- Insertar tutores de empresa
INSERT INTO tutor_empresa (id, usuario_id, empresa_id, cargo, departamento) VALUES
(1, 4, 1, 'Responsable de Desarrollo', 'IT'),
(2, 5, 2, 'Jefe de Proyectos', 'Desarrollo');

-- Insertar coordinador
INSERT INTO coordinador (id, usuario_id, centro_educativo, departamento) VALUES
(1, 6, 'IES Castelar', 'Informática');

-- Insertar competencias para DAW
INSERT INTO competencia (id, codigo, nombre, categoria, descripcion, ciclo_formativo) VALUES
(1, 'COMP-BE-01', 'Desarrollo Backend', 'TECNICA', 'Capacidad para desarrollar aplicaciones del lado del servidor', 'Desarrollo de Aplicaciones Web'),
(2, 'COMP-FE-01', 'Desarrollo Frontend', 'TECNICA', 'Capacidad para desarrollar interfaces de usuario', 'Desarrollo de Aplicaciones Web'),
(3, 'COMP-BD-01', 'Gestión de Bases de Datos', 'TECNICA', 'Diseño y gestión de bases de datos relacionales', 'Desarrollo de Aplicaciones Web'),
(4, 'COMP-API-01', 'Desarrollo de APIs REST', 'TECNICA', 'Creación de servicios web RESTful', 'Desarrollo de Aplicaciones Web'),
(5, 'COMP-TE-01', 'Trabajo en Equipo', 'TRANSVERSAL', 'Capacidad para colaborar efectivamente', 'Desarrollo de Aplicaciones Web'),
(6, 'COMP-PR-01', 'Resolución de Problemas', 'TRANSVERSAL', 'Análisis y resolución de problemas técnicos', 'Desarrollo de Aplicaciones Web'),
(7, 'COMP-AC-01', 'Autonomía y Responsabilidad', 'ACTITUDINAL', 'Capacidad de trabajar de forma autónoma', 'Desarrollo de Aplicaciones Web'),
(8, 'COMP-AC-02', 'Aprendizaje Continuo', 'ACTITUDINAL', 'Actitud proactiva hacia el aprendizaje', 'Desarrollo de Aplicaciones Web');

-- Insertar asignaciones de prácticas
INSERT INTO asignacion (id, estudiante_id, tutor_empresa_id, empresa_id, fecha_inicio, fecha_fin, horas_requeridas, horas_realizadas, estado) VALUES
(1, 1, 1, 1, '2025-01-15', '2025-05-15', 500, 0, 'ACTIVA'),
(2, 2, 1, 1, '2025-01-15', '2025-05-15', 500, 0, 'ACTIVA'),
(3, 3, 2, 2, '2025-01-20', '2025-05-20', 500, 0, 'ACTIVA');

-- Insertar algunas fichas de seguimiento de ejemplo
INSERT INTO ficha_seguimiento (id, asignacion_id, fecha, hora_entrada, hora_salida, descripcion, competencias, estado) VALUES
(1, 1, '2025-01-15', '09:00:00', '17:00:00', 
'Primer día de prácticas. Reunión de bienvenida con el equipo. Configuración del entorno de desarrollo local. Instalación de herramientas necesarias: VS Code, Git, Node.js, Docker. Revisión de la documentación del proyecto principal.',
'1,2,3', 'PENDIENTE'),
(2, 1, '2025-01-16', '09:00:00', '17:00:00',
'Análisis del código base del proyecto. Reunión con el equipo de desarrollo para entender la arquitectura del sistema. Clonación del repositorio y primera ejecución en local. Resolución de problemas de configuración.',
'1,2,6', 'PENDIENTE'),
(3, 2, '2025-01-15', '08:30:00', '16:30:00',
'Introducción a la empresa y al equipo. Tour por las instalaciones. Configuración de credenciales y accesos. Instalación del entorno de desarrollo. Primeros pasos con React.',
'2,7', 'PENDIENTE');

-- Reactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;
