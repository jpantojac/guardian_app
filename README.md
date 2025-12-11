# GuardianApp - WebGIS Participativo de Seguridad

Plataforma ciudadana para el reporte, visualización y análisis de incidentes de seguridad en Bogotá. Desarrollada con Laravel 11, PostgreSQL/PostGIS y Leaflet, enfocada en la privacidad, la usabilidad y la recolección responsable de datos.

---

## 📋 Requisitos del Sistema

Para ejecutar este proyecto localmente, recomendamos usar **Laragon** en Windows.

- **PHP**: Versión 8.2 o superior.
- **Base de Datos**: PostgreSQL 13+ (Se recomienda usar el incluido en Laragon o instalarlo por separado).
- **Extensión Geográfica**: PostGIS (Debe estar habilitada en PostgreSQL).
- **Dependencias**: Composer y Node.js.

---

## 🛠️ Guía de Instalación Paso a Paso

### 1. Configuración del Entorno (Laragon)

1.  Instala Laragon Full.
2.  Asegúrate de tener PHP 8.2+ seleccionado en `Menu > PHP`.
3.  Asegúrate de tener PostgreSQL y PostGIS instalados.
    *   *Nota*: Si usas el PostgreSQL de Laragon, verifica que la extensión PostGIS esté disponible. Si no, instala PostgreSQL EnterpriseDB por separado y conéctalo.

**Configuración Crítica para Archivos:**
Para evitar errores al subir fotos ("Path cannot be empty"), debes configurar una carpeta temporal válida:
1.  En Laragon: `Menu > PHP > php.ini`.
2.  Busca `upload_tmp_dir`.
3.  Descomenta la línea y asigna una ruta válida:
    ```ini
    upload_tmp_dir = "C:/laragon/tmp"
    ```
4.  Reinicia los servicios de Laragon.

### 2. Clonar y Preparar el Proyecto

```bash
# 1. Clonar repositorio
git clone <url-del-repo> guardianapp
cd guardianapp

# 2. Instalar dependencias backend (PHP)
composer install

# 3. Copiar archivo de entorno
cp .env.example .env

# 4. Generar llave de aplicación
php artisan key:generate
```

### 3. Configuración de Base de Datos

1.  Crea una base de datos en PostgreSQL llamada `guardianapp`.
2.  Habilita la extensión PostGIS en esa base de datos (puedes usar PgAdmin o DBeaver):
    ```sql
    CREATE EXTENSION postgis;
    ```
3.  Edita el archivo `.env` con tus credenciales:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=guardianapp
    DB_USERNAME=postgres
    DB_PASSWORD=tu_contraseña
    ```

### 4. Migraciones y Datos Iniciales

Ejecuta el siguiente comando para crear las tablas, importar las localidades de Bogotá (Shapefile) y crear datos de prueba:

```bash
php artisan migrate:fresh --seed
```

*Este proceso puede tardar unos segundos mientras importa la geometría de las localidades.*

### 5. Configurar Almacenamiento

Para que las fotos subidas sean visibles en el navegador, debes crear el enlace simbólico:

```bash
php artisan storage:link
```

---

## 🚀 Ejecución

Si usas Laragon, el proyecto debería estar accesible en `http://guardianapp.test` (si la carpeta se llama `guardianapp`).

Si prefieres usar el servidor nativo de Laravel:

```bash
php artisan serve
```
Accede a `http://localhost:8000`.

---

## 💡 Funcionalidades Principales

### Para el Ciudadano:
- **Reporte Anónimo o Identificado**: Opción de reportar sin crear cuenta o iniciar sesión para seguimiento.
- **Georreferenciación Precisa**: Uso de mapa interactivo para ubicar el incidente.
- **Evidencia Visual**: Carga de fotos con validación de seguridad.
- **Filtros Avanzados**: Filtrado por radio de distancia (km), tiempo (1h, 6h, 24h) y categoría.
- **Privacidad**: Los reportes se muestran agregados y ciertos datos sensibles pueden ofuscarse.

### Medidas de Calidad y Seguridad:
- **CAPTCHA Matemático**: Validación simple para prevenir spam automatizado en reportes anónimos.
- **Mensajes de Responsabilidad**: Recordatorios visuales sobre la importancia de la veracidad de los datos.
- **Validación de Archivos**: Controles estrictos sobre tipos y tamaños de imágenes subidas.

---

## 🧪 Usuarios de Prueba

El sistema viene precargado con estos usuarios:

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Admin** | admin@webgis.local | `SecurePass123!` |
| **Moderador** | moderator@webgis.local | `SecurePass123!` |
| **Ciudadano** | ciudadano@webgis.local | `SecurePass123!` |

---

## 📂 Estructura del Proyecto

- `app/Http/Controllers/IncidentWebController.php`: Lógica principal de reporte y almacenamiento.
- `resources/views/dashboard.blade.php`: Interfaz principal (Mapa, Filtros, Modales).
- `database/seeders`: Seeds para usuarios, categorías e incidentes de prueba.
- `storage/app/public/evidence_photos`: Ubicación física de las imágenes subidas.

