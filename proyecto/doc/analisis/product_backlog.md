# Product Backlog - Sistema FCT

**Proyecto:** Sistema de Gestión de Prácticas FCT  
**Desarrollador:** Alberto Jiménez Hernández  
**Última Actualización:** 17/11/2025

---

## Leyenda de Estados
- ✅ **COMPLETADA** - Implementada y funcionando
- 🚧 **EN PROGRESO** - Actualmente en desarrollo
- 📋 **PENDIENTE** - Planificada pero no iniciada
- ⏸️ **POSPUESTA** - Aplazada para futuros sprints

## Leyenda de Prioridades
- 🔴 **CRÍTICA** - Funcionalidad esencial del sistema
- 🟠 **ALTA** - Importante para el funcionamiento
- 🟡 **MEDIA** - Mejora significativa
- 🟢 **BAJA** - Características adicionales

---

## SPRINT 1 (Completado) ✅

### HU-01: Autenticación de Usuarios ✅
**Estado:** COMPLETADA  
**Prioridad:** 🔴 CRÍTICA  
**Sprint:** 1  
**Requerimiento:** RF-001  
**Estimación:** 8 puntos

**Como** usuario del sistema  
**Quiero** poder iniciar sesión con mi correo electrónico y contraseña  
**Para** acceder a las funcionalidades según mi rol

**Criterios de Aceptación:**
- [x] Pantalla de login con campos para email y contraseña
- [x] Validación de formato de email
- [x] Contraseña mínimo 8 caracteres
- [x] Generación de sesión PHP tras login correcto
- [x] Mensaje 'Credenciales inválidas' si fallan
- [x] Redirección al dashboard según rol
- [x] Enlace "Olvidé mi contraseña"
- [x] Timeout de sesión después de 1 hora

---

### HU-02: Registro de Ficha de Seguimiento Diaria ✅
**Estado:** COMPLETADA  
**Prioridad:** 🔴 CRÍTICA  
**Sprint:** 1  
**Requerimiento:** RF-003  
**Estimación:** 13 puntos

**Como** estudiante en prácticas  
**Quiero** registrar las actividades que realizo cada día en la empresa  
**Para** llevar un control de mi aprendizaje y que mi tutor pueda validarlas

**Criterios de Aceptación:**
- [x] Formulario de 'Nueva Ficha de Seguimiento'
- [x] Fecha actual por defecto
- [x] Hora de entrada y hora de salida
- [x] Campo descripción (mínimo 50 caracteres)
- [x] Selector múltiple de competencias
- [x] Campo opcional de dificultades
- [x] Valoración personal 1-5 estrellas
- [x] Estado 'Pendiente de validación' al guardar
- [x] Notificación al tutor de empresa
- [x] Edición permitida hasta validación
- [x] Solo lectura una vez validada

---

### HU-03: Validación de Fichas de Seguimiento ✅
**Estado:** COMPLETADA  
**Prioridad:** 🔴 CRÍTICA  
**Sprint:** 1  
**Requerimiento:** RF-003  
**Estimación:** 13 puntos

**Como** tutor de empresa  
**Quiero** revisar y validar las fichas de seguimiento de mis estudiantes  
**Para** verificar que las actividades registradas son correctas y apropiadas

**Criterios de Aceptación:**
- [x] Lista de 'Fichas Pendientes de Validación'
- [x] Muestra estudiante, fecha, resumen
- [x] Detalle completo al hacer clic
- [x] Visualiza fecha, horario, descripción, competencias
- [x] Tutor puede añadir comentarios
- [x] Botones 'Aprobar' o 'Rechazar'
- [x] Si aprueba → estado 'Validada'
- [x] Si rechaza → motivo obligatorio + estado 'Pendiente de corrección'
- [x] Notificación al estudiante
- [x] Horas sumadas al contador total

---

## SPRINT 2 (Planificado) 📋

### HU-04: Visualización de Estudiantes Asignados 📋
**Estado:** PENDIENTE  
**Prioridad:** 🟠 ALTA  
**Sprint:** 2  
**Requerimiento:** RF-004  
**Estimación:** 8 puntos

**Como** tutor de empresa  
**Quiero** ver un listado de todos los estudiantes asignados a mi supervisión  
**Para** poder acceder rápidamente a su información y seguimiento

**Criterios de Aceptación:**
- [ ] Sección 'Mis Estudiantes'
- [ ] Lista con foto, nombre completo, ciclo formativo, fechas
- [ ] Indicador visual de progreso (% horas)
- [ ] Ordenación por nombre, fecha, progreso
- [ ] Campo de búsqueda por nombre
- [ ] Clic en estudiante abre perfil detallado
- [ ] Perfil muestra datos personales, competencias, historial
- [ ] Número de fichas pendientes por estudiante
- [ ] Indicador de color si hay incidencias

**Notas de Implementación:**
- Ya existe parte del código en el dashboard del tutor
- Necesita vista completa dedicada
- Agregar filtros y búsqueda
- Implementar perfil detallado del estudiante

---

### HU-05: Control de Asistencia y Horas 📋
**Estado:** PENDIENTE  
**Prioridad:** 🟠 ALTA  
**Sprint:** 2  
**Requerimiento:** RF-005  
**Estimación:** 13 puntos

**Como** estudiante  
**Quiero** visualizar un resumen de mis horas acumuladas y mi asistencia  
**Para** saber cuántas horas me faltan para completar las prácticas

**Criterios de Aceptación:**
- [ ] Sección 'Mi Progreso'
- [ ] Indicador visual circular con % completado
- [ ] Muestra horas realizadas / horas totales
- [ ] Número de días de asistencia
- [ ] Número de faltas (justificadas/sin justificar)
- [ ] Gráfico de barras con horas por semana (último mes)
- [ ] Lista de últimas 10 fichas con fecha y horas
- [ ] Alerta si horas están por debajo de lo esperado
- [ ] Estimación de fecha de finalización
- [ ] Botón para exportar registro a PDF

**Notas de Implementación:**
- Usar Chart.js o similar para gráficos
- Calcular progreso esperado vs real
- Generar PDF con librería FPDF o similar
- Dashboard ya muestra datos básicos, ampliar funcionalidad

---

### HU-06: Gestión de Incidencias 📋
**Estado:** PENDIENTE  
**Prioridad:** 🟠 ALTA  
**Sprint:** 2  
**Requerimiento:** RF-007  
**Estimación:** 8 puntos

**Como** estudiante o tutor  
**Quiero** reportar incidencias o problemas que surjan durante las prácticas  
**Para** que el coordinador pueda intervenir y resolverlas

**Criterios de Aceptación:**
- [ ] Botón 'Reportar Incidencia' visible en dashboard
- [ ] Formulario con tipo (lista predefinida), descripción, urgencia
- [ ] Opción de adjuntar documentos/imágenes
- [ ] Notificación al coordinador FCT
- [ ] Estado inicial 'Abierta'
- [ ] Listado de incidencias propias con estado
- [ ] Coordinador puede cambiar estado (En proceso/Resuelta)
- [ ] Coordinador puede añadir comentarios
- [ ] Notificaciones de cambios de estado
- [ ] Solución visible cuando esté resuelta

**Notas de Implementación:**
- Tabla incidencia ya creada en BD
- Implementar subida de archivos
- Sistema de notificaciones
- Vista para coordinador

---

## SPRINT 3 (Planificado) 📋

### HU-07: Evaluación de Competencias 📋
**Estado:** PENDIENTE  
**Prioridad:** 🟠 ALTA  
**Sprint:** 3  
**Requerimiento:** RF-008  
**Estimación:** 13 puntos

**Como** tutor de empresa  
**Quiero** evaluar las competencias profesionales del estudiante  
**Para** proporcionar un feedback estructurado de su desempeño

**Criterios de Aceptación:**
- [ ] Acceso a 'Evaluaciones' desde perfil de estudiante
- [ ] Listado de competencias predefinidas según ciclo
- [ ] Escala: No observado/En desarrollo/Logrado/Destacado
- [ ] Campo de comentarios por competencia
- [ ] Secciones: conocimientos técnicos, habilidades, actitud
- [ ] Área de 'Observaciones generales'
- [ ] Guardar como borrador o enviar definitivo
- [ ] Evaluación visible para coordinador y estudiante
- [ ] Coordinador puede revisar todas las evaluaciones
- [ ] Estudiante puede ver pero no modificar

---

### HU-08: Sistema de Mensajería Interna 📋
**Estado:** PENDIENTE  
**Prioridad:** 🟡 MEDIA  
**Sprint:** 3  
**Requerimiento:** RF-006  
**Estimación:** 13 puntos

**Como** usuario del sistema  
**Quiero** enviar mensajes a otros usuarios  
**Para** comunicarme de forma rápida y centralizada

**Criterios de Aceptación:**
- [ ] Botón 'Mensajes' en navegación
- [ ] Bandeja de entrada con mensajes recibidos
- [ ] Mensajes enviados en pestaña separada
- [ ] Botón 'Nuevo Mensaje'
- [ ] Selector de destinatario
- [ ] Campo asunto y contenido
- [ ] Indicador de mensajes no leídos
- [ ] Marcar como leído al abrir
- [ ] Responder a mensajes
- [ ] Borrar mensajes
- [ ] Búsqueda de mensajes

---

### HU-09: Notificaciones Automáticas 📋
**Estado:** PENDIENTE  
**Prioridad:** 🟡 MEDIA  
**Sprint:** 3  
**Requerimiento:** RF-014  
**Estimación:** 8 puntos

**Como** usuario del sistema  
**Quiero** recibir notificaciones de eventos importantes  
**Para** estar informado sin tener que revisar constantemente

**Criterios de Aceptación:**
- [ ] Icono de notificaciones en header
- [ ] Badge con número de notificaciones sin leer
- [ ] Dropdown con últimas 5 notificaciones
- [ ] Marcar como leída
- [ ] Marcar todas como leídas
- [ ] Ver todas las notificaciones
- [ ] Tipos: ficha validada, mensaje nuevo, incidencia, etc.
- [ ] Opción de enviar email con notificaciones
- [ ] Configuración de preferencias de notificación

---

## FUNCIONALIDADES FUTURAS ⏸️

### HU-10: Gestión de Empresas 📋
**Prioridad:** 🟡 MEDIA  
**Requerimiento:** RF-010  
**Estimación:** 13 puntos

**Como** coordinador FCT  
**Quiero** gestionar las empresas colaboradoras  
**Para** mantener actualizada la base de datos de convenios

---

### HU-11: Asignación de Estudiantes a Plazas 📋
**Prioridad:** 🟠 ALTA  
**Requerimiento:** RF-011  
**Estimación:** 13 puntos

**Como** coordinador FCT  
**Quiero** asignar estudiantes a plazas de prácticas  
**Para** organizar el periodo FCT

---

### HU-12: Dashboard con Indicadores 📋
**Prioridad:** 🟡 MEDIA  
**Requerimiento:** RF-013  
**Estimación:** 8 puntos

**Como** coordinador FCT  
**Quiero** ver estadísticas globales del sistema  
**Para** tomar decisiones informadas

---

### HU-13: Generación de Documentos PDF 📋
**Prioridad:** 🟡 MEDIA  
**Requerimiento:** RF-009  
**Estimación:** 13 puntos

**Como** coordinador FCT  
**Quiero** generar documentación oficial automáticamente  
**Para** agilizar los procesos administrativos

**Documentos:**
- [ ] Convenio de prácticas
- [ ] Certificado de prácticas
- [ ] Informe de seguimiento
- [ ] Evaluación final

---

### HU-14: Exportación de Datos 📋
**Prioridad:** 🟢 BAJA  
**Requerimiento:** RF-015  
**Estimación:** 8 puntos

**Como** coordinador FCT  
**Quiero** exportar datos a formatos estándar  
**Para** realizar análisis externos

**Formatos:**
- [ ] Excel (.xlsx)
- [ ] CSV
- [ ] PDF

---

### HU-15: Histórico de Actividades 📋
**Prioridad:** 🟢 BAJA  
**Requerimiento:** RF-012  
**Estimación:** 5 puntos

**Como** usuario del sistema  
**Quiero** consultar el histórico de mis actividades  
**Para** revisar acciones pasadas

---

### HU-16: Recuperación de Contraseña 📋
**Prioridad:** 🟡 MEDIA  
**Requerimiento:** RF-001  
**Estimación:** 8 puntos

**Como** usuario  
**Quiero** poder recuperar mi contraseña  
**Para** acceder al sistema si la olvido

---

### HU-17: Perfil de Usuario 📋
**Prioridad:** 🟡 MEDIA  
**Requerimiento:** RF-002  
**Estimación:** 5 puntos

**Como** usuario  
**Quiero** editar mi perfil  
**Para** mantener mis datos actualizados

---

### HU-18: Calendario de Prácticas 📋
**Prioridad:** 🟢 BAJA  
**Requerimiento:** Nuevo  
**Estimación:** 8 puntos

**Como** estudiante  
**Quiero** ver un calendario de mis prácticas  
**Para** visualizar fechas importantes

---

## Resumen del Backlog

### Por Estado
- ✅ **Completadas:** 3 historias (HU-01, HU-02, HU-03)
- 📋 **Pendientes:** 15 historias
- **Total:** 18 historias de usuario

### Por Prioridad
- 🔴 **CRÍTICA:** 3 historias (todas completadas)
- 🟠 **ALTA:** 5 historias
- 🟡 **MEDIA:** 8 historias
- 🟢 **BAJA:** 2 historias

### Por Sprint
- **Sprint 1:** 3 historias ✅ COMPLETADO
- **Sprint 2:** 3 historias 📋 PLANIFICADO
- **Sprint 3:** 3 historias 📋 PLANIFICADO
- **Futuros:** 9 historias ⏸️

---

## Notas de Planificación

### Velocidad del Equipo
- **Sprint 1:** 34 puntos completados
- **Estimación Sprint 2:** 29 puntos
- **Estimación Sprint 3:** 34 puntos

### Dependencias
- HU-05 depende de HU-02 y HU-03 ✅
- HU-07 depende de HU-04 📋
- HU-09 depende de HU-06, HU-07, HU-08 📋
- HU-13 puede implementarse en paralelo

### Riesgos Identificados
1. **Generación de PDFs** puede requerir más tiempo del estimado
2. **Sistema de notificaciones** requiere configuración de email
3. **Subida de archivos** necesita configuración de permisos

---

_Documento actualizado: 17/11/2025_
