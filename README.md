# Midascen - Sistema de Gestión de Dispositivos

## ¿Qué hace midascen?

Midascen es una aplicación de gestión de inventario y movimientos de dispositivos diseñada para organizaciones. Permite llevar un control exhaustivo de:

- **Inventario:** Registro de dispositivos con su número de serie, IMEI, condición, y disponibilidad.
- **Catalogación:** Organización mediante Marcas, Categorías y Modelos.
- **Movimientos:** Seguimiento de entradas y salidas de dispositivos para saber qué usuario o destinatario tiene asignado cada equipo.
- **Administración Inteligente:** Todo gestionado a través de un panel de administración completo e intuitivo basado en Filament.

## Tecnologías usadas

- **Backend:** Laravel 12 (PHP 8.2+)
- **Panel de Administración:** Filament v3
- **Base de Datos:** PostgreSQL (en Docker) / SQLite (por defecto en local sin Docker)
- **Frontend / Assets:** Vite, Tailwind CSS
- **Infraestructura:** Docker & Docker Compose (Nginx, App, DB, Queue)

## Cómo instalarlo y configurarlo

Midascen está preparado para desplegarse fácilmente usando Docker, lo que simplifica la configuración del entorno.

### Requisitos Previos

- Git
- Docker y Docker Compose instalados en tu máquina.

### Pasos de Instalación (Con Docker)

1. **Clona el repositorio:**
   ```bash
   git clone <URL_DEL_REPOSITORIO> midascen
   cd midascen
   ```

2. **Prepara las variables de entorno para Docker:**
   Copia el archivo de ejemplo para crear el archivo `.env.docker`.
   ```bash
   cp .env.example .env.docker
   ```
   *Nota: Revisa `.env.docker` si necesitas cambiar contraseñas o puertos por defecto.*

3. **Ejecuta el script de despliegue automatizado:**
   El proyecto incluye un script `deploy.sh` que construirá las imágenes, levantará los contenedores, instalará dependencias, ejecutará migraciones y limpiará cachés.
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

4. **Crea un usuario administrador:**
   Para poder iniciar sesión en el panel de Filament, necesitas crear un usuario admin. Ejecuta el siguiente comando y sigue las instrucciones en consola:
   ```bash
   docker exec -it midascen_app php artisan make:filament-user
   ```

5. **Accede a la aplicación:**
   Abre tu navegador web y visita la IP de tu servidor o `http://localhost`. El panel de administración se encuentra en `http://localhost/admin` (o la ruta configurada).

### Pasos de Instalación (Modo Local Tradicional - Sin Docker)

Si prefieres ejecutarlo usando PHP y Composer localmente:

1. Clona el repositorio y entra a la carpeta.
2. Copia `.env.example` a `.env`: `cp .env.example .env`
3. Instala las dependencias: `composer install` y `npm install`
4. Genera la key de la app: `php artisan key:generate`
5. Configura tu base de datos en el archivo `.env` (ej. cambia a SQLite, MySQL, o Postgres).
6. Ejecuta las migraciones: `php artisan migrate`
7. Compila los assets: `npm run build`
8. Crea el usuario admin: `php artisan make:filament-user`
9. Inicia el servidor de desarrollo: `php artisan serve`

## Capturas de pantalla o demo

> *(Añade aquí imágenes de la interfaz del panel de Filament, tablas de dispositivos, formularios de movimientos, etc.)*

*Ejemplo:*
![Dashboard de Midascen](https://via.placeholder.com/800x400.png?text=Dashboard+de+Midascen+Filament)
![Gestión de Dispositivos](https://via.placeholder.com/800x400.png?text=Listado+de+Dispositivos)
