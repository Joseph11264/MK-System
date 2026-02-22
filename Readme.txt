=========================================================================
SISTEMA MK - DOCUMENTACIÓN Y REGISTRO DE CAMBIOS
=========================================================================

1. DESCRIPCIÓN DEL PROYECTO
---------------------------
Sistema administrativo web desarrollado en Laravel para la gestión de almacén.
Principales módulos:
- Requisiciones: Creación, consulta y cambio de estatus (Pendiente, En Curso, Completado).
- Usuarios: Gestión de acceso y roles (CRUD).
- Servicio Técnico: Gestión de tickets de reparación, asignación de técnicos y repuestos.
- Productos: Catálogo base de productos/repuestos para usar en los tickets.

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

C) Nuevo Módulo: Servicio Técnico (ST)
   - Migraciones: Tablas 'requisicion_st' y 'detalles_requisiciones_st'.
   - Modelos: RequisicionSt, DetalleRequisicionSt.
   - Controlador: ServicioTecnicoController (Index, Create, Store).
   - Vistas: st/index.blade.php, st/create.blade.php, st/show.blade.php.
   - Funcionalidad: Creación de tickets con cliente, equipo y fallas/repuestos.

D) Nuevo Módulo: Productos
   - Migración: Tabla 'productos'.
   - Modelo: Producto.
   - Controlador: ProductoController.
   - Vistas: productos/index.blade.php.

E) Reportes y Mantenimiento (22/02/2026)
   - Implementación de generación de PDF (DomPDF) para Requisiciones y Tickets ST.
   - Actualización de .gitignore para excluir caché de fuentes y archivos temporales.
   - Nuevas rutas y vistas para reportes (requisiciones.reporte, st.reporte).

F) Refinamiento de Lógica de Negocio (22/02/2026 - Parte 2)
   - Servicio Técnico:
     * Implementación de Tipos: Reparación vs Garantía.
     * Flujo de Cierre: Validación obligatoria de entrega de materiales y precio antes de completar.
     * Gestión de Pagos: Registro de referencia de pago y cambio de estado a 'Pagado'.
   - Requisiciones:
     * Diferenciación entre 'Requisición' (Salida) y 'Devolución' (Entrada).

4. NOTAS DE DESPLIEGUE
----------------------
- Asegurarse de ejecutar las migraciones si hay cambios en la BD.
- Verificar que los usuarios en la base de datos tengan el rol 'SuperAdmin' textualmente.
=========================================================================