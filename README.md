# GuardianApp - WebGIS Participativo de Seguridad

Plataforma ciudadana para el reporte, visualización y análisis de incidentes de seguridad en Bogotá. Desarrollada con Laravel 11, PostgreSQL/PostGIS y Leaflet, enfocada en la privacidad, la usabilidad y la recolección responsable de datos.

---

## 📋 Requisitos del Sistema

Para ejecutar este proyecto localmente, se recomienda utilizar **Laragon** en sistemas operativos Windows.

- **PHP**: Versión 8.2 o superior.
- **Base de Datos**: PostgreSQL 13+ (Se recomienda utilizar el servicio incluido en Laragon o realizar una instalación independiente).
- **Extensión Geográfica**: PostGIS (Debe encontrarse habilitada en PostgreSQL).
- **Dependencias**: Composer y Node.js.

---

## 🛠️ Guía de Instalación Paso a Paso

### 1. Configuración del Entorno (Laragon)

1.  Instalar Laragon Full.
2.  Asegurarse de tener seleccionada la versión PHP 8.2+ en `Menu > PHP`.
3.  Asegurarse de contar con PostgreSQL y PostGIS instalados.
    *   *Nota*: Si se utiliza el servicio PostgreSQL de Laragon, es necesario verificar que la extensión PostGIS se encuentre disponible. En caso contrario, se sugiere instalar PostgreSQL EnterpriseDB de forma independiente y conectarlo al entorno.

**Configuración Crítica para Archivos:**
Para evitar bloqueos al cargar fotografías ("Path cannot be empty"), se debe configurar un directorio temporal válido:
1.  En Laragon, navegar a: `Menu > PHP > php.ini`.
2.  Buscar la directiva `upload_tmp_dir`.
3.  Descomentar la línea y asignar una ruta válida en el sistema:
    ```ini
    upload_tmp_dir = "C:/laragon/tmp"
    ```
4.  Reiniciar los servicios de Laragon.

### 2. Clonar y Preparar el Proyecto

```bash
# 1. Clonar repositorio
git clone <url-del-repo> guardianapp
cd guardianapp

# 2. Instalar dependencias del backend (PHP)
composer install

# 3. Copiar archivo de entorno
cp .env.example .env

# 4. Generar llave de la aplicación
php artisan key:generate
```

### 3. Configuración de Base de Datos

1.  Crear una base de datos en PostgreSQL denominada `guardianapp`.
2.  Habilitar la extensión PostGIS en dicha base de datos (utilizando herramientas como PgAdmin o DBeaver):
    ```sql
    CREATE EXTENSION postgis;
    ```
3.  Editar el archivo `.env` para establecer las credenciales correspondientes:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=guardianapp
    DB_USERNAME=postgres
    DB_PASSWORD=contraseña_asignada
    ```

### 4. Migraciones y Datos Iniciales

Ejecutar el comando de automatización maestro para instanciar las tablas, inyectar la geometría oficial de las 20 localidades de Bogotá automáticamente (vía Seeder) y poblar la base de datos con 2,500 incidentes de prueba agrupados en "hotspots":

```bash
php artisan migrate:fresh --seed
```

*Nota: Este proceso toma directamente el volcado alojado en `database/data/localidades.sql` y lo vierte sobre PostGIS. Al finalizar la ejecución, el sistema contará con un WebGIS densamente poblado y con un Heatmap 100% operativo.*

### 5. Configurar Almacenamiento

Para permitir que las fotografías proporcionadas sean visibles a través del navegador, es indispensable crear el enlace simbólico requerido por el framework:

```bash
php artisan storage:link
```

### 6. Inyección de Datos en Vivo (Modo Demo)

Para evidenciar la reacción de los KPIs, las gráficas y el mapa de calor ante un incremento abrupto de reportes simultáneos, es posible inyectar datos de forma inmediata ejecutando la siguiente instrucción:

```bash
php artisan guardian:seed-today --count=100
```

*Nota: Este comando inyectará orgánicamente la cantidad de incidentes indicada con fecha del **día en curso**, concentrándolos algorítmicamente en los "hotspots" de la ciudad **y sin eliminar el historial** existente. Es el procedimiento idóneo para demostrar la actualización de datos y monitoreo analítico en tiempo real.*

---

## 🚀 Ejecución

En caso de utilizar Laragon, el proyecto debe encontrarse accesible a través de la dirección URL `http://guardianapp.test` (asumiendo que el nombre del directorio configurado corresponde a `guardianapp`).

Si se prefiere utilizar el servidor de desarrollo nativo de Laravel, es necesario ejecutar:

```bash
php artisan serve
```
Posteriormente, acceder mediante el navegador a `http://localhost:8000`.

---

## 💡 Funcionalidades Principales

### Para el Ciudadano:
- **Reporte Anónimo o Identificado**: Opción libre de reportar sin requerir la creación de una cuenta, o bien, iniciar sesión para un seguimiento personalizado.
- **Georreferenciación Precisa**: Uso de un mapa interactivo para seleccionar las coordenadas exactas del incidente.
- **Evidencia Visual**: Carga de material fotográfico respaldada por validaciones de seguridad.
- **Filtros Avanzados**: Filtrado paramétrico por radio de distancia (km), tiempo transcurrido (1h, 6h, 24h) y tipología de la categoría.
- **Privacidad**: Los reportes se presentan con agregación estadística, y determinados datos sensibles poseen la capacidad de ser ofuscados.

### Medidas de Calidad y Seguridad:
- **CAPTCHA Matemático**: Validación aritmética de primer nivel para prevenir interacciones de bots (spam) en formularios anónimos.
- **Mensajes de Responsabilidad**: Alertas visuales constantes sobre la importancia y repercusión de aportar datos de manera verídica.
- **Validación de Archivos**: Controles y filtros estrictos sobre las extensiones permitidas y el peso del material gráfico aportado.

---

## 🧪 Usuarios de Prueba

El sistema se encuentra precargado con los siguientes actores:

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Admin** | admin@webgis.local | `SecurePass123!` |
| **Moderador** | moderator@webgis.local | `SecurePass123!` |
| **Ciudadano** | ciudadano@webgis.local | `SecurePass123!` |

---

## 📂 Estructura del Proyecto

- `app/Http/Controllers/IncidentWebController.php`: Unidad lógica responsable del enrutamiento de reportes y validación de almacenamiento.
- `resources/views/dashboard.blade.php`: Consola visual principal de interacción (Contenedor de WebMap Leaflet, Panel de Filtros y Módulo Modal).
- `database/seeders`: Elementos de automatización (Seeds) destinados a inyectar taxonomías base y simular el volumen robusto de incidentes.
- `storage/app/public/evidence_photos`: Repositorio de resguardo físico definitivo para las capturas fotográficas admitidas por el sistema.
