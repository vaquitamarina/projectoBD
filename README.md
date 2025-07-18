# Sistema de Transporte - Proyecto Base de Datos

## Descripción
Sistema de gestión de transporte de pasajeros desarrollado en PHP con arquitectura MVC y base de datos MySQL.

## Estructura del Proyecto

### 🗂️ Estructura de Directorios
```
projectoBD/
├── bd/                     # Scripts de base de datos
│   ├── script.sql         # Script de creación de tablas
│   ├── datos.sql          # Datos de prueba
│   └── pruebas.sql        # Consultas de prueba
├── config/                # Configuración
│   ├── db.php            # Configuración de base de datos
│   └── env.php           # Variables de entorno
├── controllers/          # Controladores MVC
│   ├── controller.php    # Controlador base
│   ├── appController.php # Controlador principal de la aplicación
│   ├── formController.php # Controlador de formularios
│   ├── menuController.php # Controlador de menús
│   └── ticketController.php # Controlador de tickets
├── models/               # Modelos MVC
│   ├── model.php         # Modelo base con funciones CRUD
│   ├── modelForm.php     # Modelo para formularios
│   ├── modelUsuario.php  # Modelo de usuarios
│   ├── modelTicket.php   # Modelo de tickets
│   └── modelPasajero.php # Modelo de pasajeros
├── views/                # Vistas
│   ├── app.php           # Vista principal
│   ├── crud.php          # Vista CRUD
│   ├── form.php          # Vista de formularios
│   └── view.php          # Vista genérica
├── css/                  # Estilos CSS
│   ├── styles.css        # Estilos principales
│   └── crud.css          # Estilos para CRUD
├── index.php             # Punto de entrada principal
└── README.md             # Este archivo
```

### 🗄️ Base de Datos
La base de datos `SistemaTransporte` incluye las siguientes tablas:

#### Tablas Principales
- **`bus`**: Información de los buses (placa, clase, capacidad)
- **`ruta`**: Rutas de viaje (origen, destino, duración)
- **`viaje`**: Viajes programados (fecha, hora)
- **`pasajero`**: Información de pasajeros
- **`usuario`**: Usuarios del sistema
- **`ticket`**: Tickets de viaje
- **`asiento`**: Asientos de los buses
- **`trabajador`**: Empleados del sistema

#### Tablas de Relación
- **`viajebus`**: Relación viaje-bus
- **`viajeruta`**: Relación viaje-ruta
- **`trabajadorbus`**: Relación trabajador-bus

### 🏗️ Arquitectura MVC

#### Modelos
- **`model.php`**: Clase base con funciones CRUD genéricas
- **`modelForm.php`**: Manejo de formularios y validaciones
- **`modelUsuario.php`**: Gestión de usuarios y autenticación
- **`modelTicket.php`**: Gestión de tickets y compras
- **`modelPasajero.php`**: Gestión de pasajeros

#### Controladores
- **`controller.php`**: Controlador base
- **`appController.php`**: Controlador principal que coordina la compra de tickets
- **`formController.php`**: Manejo de formularios CRUD
- **`ticketController.php`**: Controlador específico para tickets

#### Vistas
- **`app.php`**: Vista principal de la aplicación
- **`form.php`**: Vista para formularios
- **`crud.php`**: Vista para operaciones CRUD

## 🚀 Funcionalidades Principales

### 1. Gestión de Usuarios
- Registro y autenticación de usuarios
- Perfil de usuario
- Historial de compras

### 2. Compra de Tickets
- Búsqueda de viajes disponibles
- Selección de asientos
- Registro de pasajeros
- Confirmación de compra

### 3. Gestión de Pasajeros
- Registro de pasajeros
- Búsqueda y filtrado
- Historial de viajes

### 4. Administración
- CRUD completo para todas las entidades
- Generación de formularios dinámicos
- Validaciones automáticas

## 📋 Instalación y Configuración

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd projectoBD
   ```

2. **Configurar la base de datos**
   ```sql
   -- Crear la base de datos
   CREATE DATABASE SistemaTransporte;
   USE SistemaTransporte;
   
   -- Ejecutar el script de creación
   source bd/script.sql;
   
   -- Insertar datos de prueba
   source bd/datos.sql;
   ```

3. **Configurar conexión a la base de datos**
   Editar `config/db.php` con tus credenciales:
   ```php
   $host = 'localhost';
   $dbname = 'SistemaTransporte';
   $username = 'tu_usuario';
   $password = 'tu_contraseña';
   ```

4. **Configurar servidor web**
   Apuntar el DocumentRoot a la carpeta del proyecto.

## 🔧 Uso del Sistema

### Ejemplo de Uso Básico

```php
// Crear instancia del controlador principal
$appController = new AppController();

// Mostrar página de compra de tickets
$appController->comprarTickets();

// Obtener asientos disponibles (AJAX)
$appController->asientosDisponibles();

// Procesar compra de tickets
$appController->confirmacion();
```

### Ejemplo de Uso de Modelos

```php
// Modelo de Usuario
$modelUsuario = new ModelUsuario();
$usuario = $modelUsuario->autenticarUsuario($email, $password);

// Modelo de Pasajero
$modelPasajero = new ModelPasajero();
$pasajero = $modelPasajero->crear($datosPasajero);

// Modelo de Ticket
$modelTicket = new TicketModel();
$ticket = $modelTicket->crear($datosTicket);
```

## 🧪 Archivos de Prueba

- **`ejemploUsoModelUsuario.php`**: Ejemplos de uso del modelo de usuarios
- **`ejemploUsoAppController.php`**: Ejemplos de uso del controlador principal
- **`test_form_debug.php`**: Pruebas de formularios

## 🔍 Solución de Problemas Comunes

### Error: "Tabla 'viajebus' no encontrada"
**Causa**: Inconsistencia en nombres de tablas (mayúsculas/minúsculas)
**Solución**: Todas las tablas usan nombres en minúsculas:
- `viajebus` (no `viajeBus`)
- `viajeruta` (no `viajeRuta`)
- `trabajadorbus` (no `trabajadorBus`)

### Error: "No such file or directory"
**Causa**: Rutas relativas incorrectas
**Solución**: Se usan rutas absolutas con `__DIR__`:
```php
require_once __DIR__ . '/models/model.php';
```

### Error: "Class not found"
**Causa**: Archivos no incluidos correctamente
**Solución**: Verificar que todos los `require_once` estén presentes

## 🛠️ Desarrollo y Extensión

### Agregar Nuevas Funcionalidades

1. **Crear nuevo modelo**:
   ```php
   class NuevoModelo extends Modelo {
       // Implementar métodos específicos
   }
   ```

2. **Crear nuevo controlador**:
   ```php
   class NuevoController extends Controller {
       // Implementar lógica de negocio
   }
   ```

3. **Crear nueva vista**:
   ```php
   // Vista HTML/PHP correspondiente
   ```

### Mejores Prácticas

- Usar la clase `Modelo` base para operaciones CRUD
- Implementar validaciones en los modelos
- Separar lógica de negocio en controladores
- Usar vistas para la presentación
- Manejar errores adecuadamente

## 📝 Notas Técnicas

- **Encoding**: UTF-8
- **Estándar PHP**: PSR-1, PSR-2
- **Base de datos**: MySQL con motor InnoDB
- **Seguridad**: Prepared statements para prevenir SQL injection
- **Sesiones**: Manejo de sesiones PHP para autenticación

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo LICENSE para más detalles.

---

**Desarrollado con ❤️ para el curso de Base de Datos**
