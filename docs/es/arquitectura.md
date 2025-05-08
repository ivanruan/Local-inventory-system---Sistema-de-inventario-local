# Arquitectura del Proyecto de Inventario

Este sistema de inventario está desarrollado con el framework **Laravel**, siguiendo el patrón clásico **MVC (Modelo - Vista - Controlador)**, pero reforzado con los principios de **Clean Architecture** para lograr una solución mantenible, escalable y profesional.

---

## 🧱 Arquitectura Base: MVC

### Modelo (Model)
Encapsula la lógica de acceso y manipulación de los datos. Los modelos representan las entidades principales del sistema, como productos, entradas, salidas y ubicaciones.

- Ubicación: `app/Models/`
- Ejemplo: `Producto.php`, `Entrada.php`

### Vista (View)
Son las plantillas que muestran la información al usuario. Se usan las vistas de Blade.

- Ubicación: `resources/views/`
- Ejemplo: `productos/index.blade.php`, `dashboard.blade.php`

### Controlador (Controller)
Intermedia entre los modelos y las vistas. Recibe las peticiones, procesa la lógica necesaria (directamente o por medio de servicios) y retorna la vista adecuada o una respuesta.

- Ubicación: `app/Http/Controllers/`
- Ejemplo: `ProductoController.php`, `EntradaController.php`

---

## 🧠 Extensión: Principios de Clean Architecture

### 📂 Capas añadidas:

1. **Casos de Uso (Use Cases / Services)**
   - Contienen la lógica del negocio independiente del framework.
   - Se ubican en: `app/Services/`
   - Ejemplo: `RegistrarEntradaService.php`

2. **Repositorios (Repositories)**
   - Aíslan el acceso a base de datos del resto del sistema.
   - Se define una interfaz y una implementación concreta.
   - Se ubican en: `app/Repositories/`
   - Ejemplo:
     - `Interfaces/ProductoRepositoryInterface.php`
     - `Eloquent/ProductoRepository.php`

3. **Solicitudes (Request Validation)**
   - Validan los datos de entrada.
   - Se ubican en: `app/Http/Requests/`
   - Ejemplo: `CrearProductoRequest.php`, `RegistrarEntradaRequest.php`

---

## 🗂️ Estructura de carpetas recomendada


---

## 🎯 Beneficios de esta arquitectura

- **Separación clara de responsabilidades**
- **Fácil de testear** (los servicios y repositorios pueden probarse de forma aislada)
- **Escalable**: Se puede reemplazar Laravel por otro framework manteniendo la lógica de negocio
- **Colaboración más fluida**: facilita el trabajo conjunto con otros roles (como la estudiante de ingeniería industrial)

---

## 🧪 Ejemplo de flujo de una operación

### Registrar una entrada de producto

1. El usuario accede al formulario en la vista Blade
2. El controlador `EntradaController` recibe la solicitud
3. El controlador llama a `RegistrarEntradaService`
4. El servicio valida reglas adicionales y llama al repositorio
5. El repositorio guarda los datos en la base de datos
6. Se actualiza la existencia del producto
7. Se retorna una respuesta exitosa a la vista

---

## 🔚 Conclusión

Esta arquitectura mezcla lo mejor de Laravel y los principios de Clean Architecture, brindando una base sólida para un proyecto profesional, bien organizado, fácil de mantener y que puede crecer sin volverse caótico.


