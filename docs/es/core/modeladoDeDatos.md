# Modelado de Datos

Este documento describe el modelo de datos propuesto para el sistema de inventario. Incluye las entidades principales, sus atributos y las relaciones entre ellas. Está basado en el análisis del archivo fuente proporcionado y adaptado a una arquitectura moderna con Laravel.

---

## Entidades Principales

### 1. Productos

Contiene la información básica de cada producto que será gestionado en el sistema.

**Campos:**
- `id` (PK)
- `codigo` (string, único)
- `nombre` (string)
- `unidad` (string) — unidad de medida
- `existencia` (entero) — cantidad actual en inventario
- `ubicacion_id` (FK) — ubicación física dentro del almacén

---

### 2. Ubicaciones

Define los distintos lugares donde pueden estar almacenados los productos.

**Campos:**
- `id` (PK)
- `nombre` (string)

---

### 3. Entradas

Registra cada ingreso de productos al almacén.

**Campos:**
- `id` (PK)
- `producto_id` (FK)
- `cantidad` (entero)
- `fecha` (datetime)
- `destino` (string) — opcional
- `observacion` (text) — opcional
- `usuario_id` (FK) — quien registró

---

### 4. Salidas

Registra cada egreso o retiro de productos del almacén.

**Campos:**
- `id` (PK)
- `producto_id` (FK)
- `cantidad` (entero)
- `fecha` (datetime)
- `destino` (string) — opcional
- `observacion` (text) — opcional
- `usuario_id` (FK) — quien registró

---

### 5. Usuarios

Define a los usuarios del sistema y su tipo de acceso.

**Campos:**
- `id` (PK)
- `nombre` (string)
- `correo` (string, único)
- `contraseña` (hash)
- `rol` (enum: administrador, operador, supervisor)

---

## Relaciones entre Entidades

- Un **producto** pertenece a una **ubicación**
- Una **ubicación** puede tener muchos **productos**
- Un **producto** puede tener muchas **entradas** y muchas **salidas**
- Un **usuario** puede registrar múltiples **entradas** y **salidas**

---

## Diagrama Entidad-Relación (ER)

*(Este apartado puede completarse con un diagrama visual usando herramientas como dbdiagram.io, Draw.io, o Laravel Model Generator)*

---

## Consideraciones Adicionales

- Todas las fechas se almacenan en UTC para facilitar el registro y auditoría.
- Las observaciones son campos opcionales para permitir flexibilidad en el control.
- Se usará control de claves foráneas para asegurar integridad referencial en la base de datos.

---

**Última actualización:** Abril 2025

