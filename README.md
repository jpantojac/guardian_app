# GuardianApp - WebGIS Participativo

Plataforma de reporte ciudadano de incidentes de seguridad para Bogotá, construida con Laravel, PostgreSQL/PostGIS y Leaflet.

## Requisitos Previos

- PHP 8.2+
- Composer
- PostgreSQL 13+ con extensión PostGIS habilitada
- Node.js & NPM

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repo>
   cd guardianapp
   ```

2. **Instalar Dependencias PHP**
   ```bash
   composer install
   ```
   *Nota: Si tienes problemas de red, configura un proxy o verifica tu conexión.*

3. **Configurar Entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configura las credenciales de base de datos en `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=guardianapp
   DB_USERNAME=postgres
   DB_PASSWORD=PC6393530
   ```

4. **Migraciones y Seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```
   Esto creará las tablas y poblará la base de datos con:
   - Usuarios (Admin, Moderador, Ciudadano)
   - Categorías de delitos
   - 150 incidentes de prueba en Bogotá

5. **Ejecutar Servidor**
   ```bash
   php artisan serve
   ```

## Usuarios de Prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@webgis.local | SecurePass123! |
| Moderador | moderator@webgis.local | SecurePass123! |
| Ciudadano | ciudadano@webgis.local | SecurePass123! |

## Arquitectura

- **Backend**: Laravel 11 (API RESTful)
- **Base de Datos**: PostgreSQL + PostGIS (Geometrías)
- **Frontend**: Blade + Leaflet JS (Mapas interactivos)
- **Seguridad**: Laravel Sanctum (Auth), Audit Logs (Observers)

## Privacidad

- Los reportes públicos tienen "ruido" geoespacial o se agrupan para proteger la ubicación exacta de las víctimas.
- Se mantiene un registro de auditoría (Audit Logs) de todas las acciones.
