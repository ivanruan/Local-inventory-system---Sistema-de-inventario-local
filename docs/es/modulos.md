
# Módulos del Sistema de Inventario

Este documento describe la estructura de módulos del sistema de inventario, incluyendo funcionalidades clave y planes futuros.

---

## 🛠 Módulos Principales

### 1. **Módulo de Productos**
- **Propósito**: Gestionar la información base de los productos.
- **Funcionalidades**:
  - Crear/editar/eliminar productos.
  - Asociar productos a categorías (HDT, EDT, CON, EPP) y ubicaciones.
  - Definir especificaciones técnicas, marca, y códigos únicos.
  - Configurar stock mínimo, máximo y de seguridad.

### 2. **Módulo de Inventario**
- **Propósito**: Monitorear y controlar el stock en tiempo real.
- **Funcionalidades**:
  - Visualizar stock actual por producto/ubicación.
  - Alertas automáticas para stock bajo o crítico.
  - Histórico de movimientos (entradas/salidas).

### 3. **Módulo de Movimientos** *(Unifica Entradas/Salidas)*
- **Propósito**: Registrar transacciones de inventario.
- **Funcionalidades**:
  - Registrar **entradas** (compra, donación) vinculadas a proveedores.
  - Registrar **salidas** (consumo, préstamo) asociadas a proyectos.
  - Generar comprobantes (PDF/Excel) para auditoría.

### 4. **Módulo de Ubicaciones**
- **Propósito**: Gestionar la distribución física de los productos.
- **Funcionalidades**:
  - Definir ubicaciones jerárquicas (ej: `A2-Nivel 4`).
  - Mapa visual de almacenes/secciones.
  - Búsqueda de productos por ubicación.

---

## 🔜 Módulos Futuros

### 5. **Módulo de Proveedores**
- **Propósito**: Centralizar información de proveedores.
- **Funcionalidades**:
  - Catálogo de proveedores con datos de contacto.
  - Historial de compras y rendimiento.
  - Integración con entradas de inventario.

### 6. **Módulo de Mantenimientos**
- **Propósito**: Programar y registrar mantenimientos preventivos.
- **Funcionalidades**:
  - Calendario de revisiones (ej: limpieza de generadores).
  - Bitácoras de mantenimiento con evidencia (fotos/docs).
  - Alertas para próximas actividades.

### 7. **Módulo de Alertas/Notificaciones**
- **Propósito**: Automatizar respuestas a eventos críticos.
- **Funcionalidades**:
  - Notificaciones por correo/API para:
    - Stock por debajo del mínimo.
    - Mantenimientos pendientes.
    - Movimientos inusuales.

### 8. **Módulo de Usuarios y Roles**
- **Propósito**: Gestionar acceso y permisos.
- **Funcionalidades**:
  - Roles predefinidos (Almacenista, Supervisor, Gerente).
  - Permisos granulares (ej: "Aprobar salidas", "Editar productos").

### 9. **Módulo de Reportes**
- **Propósito**: Generar análisis para la toma de decisiones.
- **Funcionalidades**:
  - Reportes de rotación de inventario.
  - Histórico de movimientos por proyecto/proveedor.
  - Costos asociados a stock obsoleto.

---

## 🚀 Recomendaciones Clave
- **Unificar Entradas/Salidas**: Simplifica la trazabilidad y reduce redundancias.
- **Priorizar Proveedores y Alertas**: Evita datos duplicados y mejora la proactividad.
- **Integrar Mantenimientos**: Critico para equipos costosos (generadores, herramientas).
- **Usar Índices y Triggers**: Optimiza consultas de stock y actualizaciones en tiempo real.

---

