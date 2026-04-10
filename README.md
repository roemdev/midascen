# Midascen - Sistema de Gestión de Dispositivos

Midascen es una aplicación desarrollada en **Laravel 12** con un panel de administración basado en **Filament**. Está diseñada para gestionar el inventario y los movimientos de dispositivos dentro de una organización.

## 🚀 Funciones Principales

- **Gestión del Inventario:**
  - Registro de **Dispositivos** con su número de serie, IMEI, condición, y disponibilidad.
  - Catalogación a través de **Marcas**, **Categorías**, y **Modelos**.

- **Control de Movimientos:**
  - Registro de **Movimientos de Dispositivos** (entradas/salidas) para saber quién tiene asignado qué dispositivo.
  - Asignación a **Destinatarios** y registro de fechas de entrega y devolución.

- **Panel de Administración Inteligente:**
  - Panel completo para gestionar todos los recursos (Usuarios, Dispositivos, Movimientos, etc.) mediante Filament.

- **Despliegue Sencillo con Docker:**
  - La aplicación está dockerizada para un fácil despliegue en entornos de desarrollo y producción usando PostgreSQL, Nginx y colas (Queue).

## 📋 Requisitos Previos

Para desplegar la aplicación fácilmente usando Docker, necesitarás:
- [Docker](https://docs.docker.com/get-docker/) instalado en tu máquina.
- [Docker Compose](https://docs.docker.com/compose/install/) instalado en tu máquina.
- Git (para clonar el repositorio).

## 🔧 Cómo clonar y desplegar

Sigue estos pasos para instalar y poner en marcha el proyecto:

1. **Clona el repositorio:**
   ```bash
   git clone <URL_DEL_REPOSITORIO> midascen
   cd midascen
   ```

2. **Prepara el entorno Docker:**
   Copia el archivo de ejemplo para crear el `.env.docker`.
   ```bash
   cp .env.example .env.docker
   ```
   > **Nota:** Modifica las variables de entorno en `.env.docker` si necesitas cambiar la configuración por defecto de la base de datos, URLs o correos, etc. Especialmente fíjate en las variables de Base de Datos para asegurar que conecten adecuadamente (si el default está en SQLite en el ejemplo, puede que debas configurar credenciales de PostgreSQL de Docker).

3. **Ejecuta el script de despliegue:**
   El proyecto incluye un script `deploy.sh` que construye las imágenes, levanta los contenedores, instala dependencias (composer y npm), corre las migraciones y genera la caché.
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

4. **Crea el usuario administrador:**
   Una vez que el script finalice (te mostrará que el contenedor está corriendo), crea un usuario para poder acceder al panel de Filament:
   ```bash
   docker exec -it midascen_app php artisan make:filament-user
   ```

5. **Accede a la aplicación:**
   Puedes ingresar a la aplicación web a través de tu navegador:
   - URL de la aplicación: `http://localhost` (o en la IP del host mostrada por `deploy.sh`).
   - El panel de administración estará en `http://localhost/admin` (o la ruta que esté configurada para Filament).

## 🧰 Comandos útiles

- Bajar todos los contenedores:
  ```bash
  docker compose down
  ```
- Ver los logs de la aplicación:
  ```bash
  docker compose logs -f app
  ```
- Ingresar a la consola del contenedor de la app:
  ```bash
  docker exec -it midascen_app /bin/bash
  ```

---
*Para más información sobre el Framework base, visita la [Documentación de Laravel](https://laravel.com/docs).*
