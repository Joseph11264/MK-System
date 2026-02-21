=========================================================================
SISTEMA MK - DOCUMENTACIÓN Y REGISTRO DE CAMBIOS
=========================================================================

1. DESCRIPCIÓN DEL PROYECTO
---------------------------
Sistema administrativo web desarrollado en Laravel para la gestión de almacén.
Principales módulos:
- Requisiciones: Creación, consulta y cambio de estatus (Pendiente, En Curso, Completado).
- Usuarios: Gestión de acceso y roles (CRUD).
- Servicio Técnico: (En desarrollo) Gestión de reparaciones.

2. ESTRUCTURA DE CÓDIGO RELEVANTE
---------------------------------
- Controladores: app/Http/Controllers/ (Lógica de negocio: RequisicionController, UserController).
- Modelos: app/Models/ (Interacción con BD: Requisicion, DetalleRequisicion, User).
- Vistas: resources/views/ (Interfaz gráfica con Blade y Bootstrap).
- Rutas: routes/web.php (Definición de URLs y protección por middleware).

3. REGISTRO DE CAMBIOS (CHANGELOG) - ÚLTIMA VERSIÓN
---------------------------------------------------
Fecha: 21/02/2026
Autor: Joseph11264 / Asistente

Cambios Realizados:
A) Refactorización de Roles de Usuario (UserController.php):
   - Se reemplazó el rol "SuperUsuario" por "SuperAdmin" para mantener consistencia.
   - Se actualizó la validación en los métodos store() y update() para aceptar 'SuperAdmin'.
   - Se renombró la función de seguridad interna a 'authorizeSuperAdmin'.

B) Documentación:
   - Creación de este archivo Readme.txt en la raíz del proyecto.

4. NOTAS DE DESPLIEGUE
----------------------
- Asegurarse de ejecutar las migraciones si hay cambios en la BD.
- Verificar que los usuarios en la base de datos tengan el rol 'SuperAdmin' textualmente.
=========================================================================