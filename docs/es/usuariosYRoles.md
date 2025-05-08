# Usuarios y Roles del Sistema de Inventario

Este documento describe los tipos de usuarios que podrán acceder al sistema de inventario, junto con sus respectivos roles, permisos y responsabilidades dentro del sistema.

---

## 🎯 Objetivo

Definir y documentar los perfiles de usuario del sistema para establecer límites de acceso, responsabilidades y permitir una mejor implementación de la seguridad lógica.

---

## 👤 Tipos de Usuarios

### 1. Administrador

**Descripción:** Usuario con acceso total al sistema. Generalmente designado por el área de TI o por la dirección de la empresa.

**Permisos:**
- Crear, editar y eliminar productos
- Registrar entradas y salidas
- Crear, editar y eliminar usuarios
- Crear y gestionar ubicaciones
- Generar todos los reportes disponibles
- Ver el historial completo del sistema

---

### 2. Supervisor

**Descripción:** Usuario que supervisa las operaciones del almacén y puede visualizar información detallada pero no modificarla.

**Permisos:**
- Consultar productos, entradas, salidas y ubicaciones
- Generar reportes
- Ver estadísticas
- Consultar el historial de movimientos

---

### 3. Operador de Almacén

**Descripción:** Usuario encargado del manejo físico del inventario. Realiza registros en tiempo real sobre movimientos de inventario.

**Permisos:**
- Registrar entradas de productos
- Registrar salidas de productos
- Consultar productos
- Consultar su propio historial de movimientos

---

### 4. Usuario Invitado (opcional)

**Descripción:** Usuario sin permisos de modificación. Utilizado para auditorías externas o visitantes temporales.

**Permisos:**
- Consultar productos
- Ver reportes limitados (lectura)
- Sin acceso a configuraciones ni historial completo

---

## 📌 Reglas Generales de Acceso

- Todos los usuarios deben autenticarse con usuario y contraseña.
- Cada usuario tiene un rol único asignado.
- El acceso está restringido por vistas, rutas y acciones en función del rol.

---

## 🛡️ Consideraciones de Seguridad

- Las contraseñas se almacenan encriptadas (bcrypt).
- El sistema utilizará middleware para proteger rutas según el rol del usuario.
- Se registra el historial de sesiones para rastrear accesos.

---

## 🔄 Mapa de Roles y Permisos

| Acción / Módulo           | Administrador | Supervisor | Operador de Almacén | Invitado |
|--------------------------|---------------|------------|----------------------|----------|
| Ver productos             | ✅             | ✅          | ✅                    | ✅        |
| Crear/editar productos    | ✅             | ❌          | ❌                    | ❌        |
| Registrar entradas        | ✅             | ❌          | ✅                    | ❌        |
| Registrar salidas         | ✅             | ❌          | ✅                    | ❌        |
| Ver reportes              | ✅             | ✅          | ❌                    | ✅*       |
| Gestión de usuarios       | ✅             | ❌          | ❌                    | ❌        |
| Gestión de ubicaciones    | ✅             | ❌          | ❌                    | ❌        |

\* El invitado podría tener acceso limitado a ciertos reportes, según configuración interna.

---

## 🔧 Posibles Mejoras Futuras

- Sistema de permisos granular (por acción)
- Registro de cambios (auditoría de quién modificó qué)
- Asignación de permisos por módulo más que por rol estático

