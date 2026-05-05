<div align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=00c8ff&height=300&section=header&text=Punto%20de%20Venta%20Pro&fontSize=50&fontAlignY=38&desc=Sistema%20ERP%20Integral%20desarrollado%20con%20Laravel%2012&descAlignY=51&descAlign=62" alt="Header" />

  <p align="center">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License" />
  </p>
</div>

---

## 🌟 Sobre el Proyecto

Este es un **Sistema de Punto de Venta (POS) y Gestión de Inventarios (ERP)** de nivel empresarial, diseñado para ofrecer una solución robusta y escalable a negocios minoristas y mayoristas. Construido sobre el framework **Laravel 12**, el sistema integra las mejores prácticas de desarrollo para garantizar seguridad, velocidad y facilidad de uso.

## ✨ Características Principales

El sistema está dividido en módulos estratégicos que cubren todas las necesidades de una operación comercial:

### 📦 Gestión de Inventario & Productos
- **Kardex:** Control detallado de entradas y salidas de mercancía.
- **Multicategoría:** Organización por Marcas, Categorías y Presentaciones.
- **Barcodes:** Generación automática de códigos de barras para productos.

### 💰 Ventas & Compras
- **Punto de Venta Dinámico:** Interfaz ágil para facturación y ventas rápidas.
- **Gestión de Compras:** Registro de proveedores y reposición de stock.
- **Comprobantes:** Generación de facturas y tickets en PDF.

### 🏧 Finanzas & Caja
- **Control de Cajas:** Apertura, cierre y arqueo de caja diario.
- **Movimientos:** Registro de ingresos y egresos extraordinarios.

### 👥 Administración & Seguridad
- **Roles & Permisos:** Gestión avanzada con Spatie (Admin, Cajero, Almacenero).
- **Clientes & Proveedores:** Directorio centralizado de contactos.
- **Logs de Actividad:** Auditoría de cada acción realizada en el sistema.

---

## 🛠️ Stack Tecnológico

- **Backend:** Laravel 12.x
- **Frontend:** Blade, Vite, Bootstrap
- **Base de Datos:** MySQL / MariaDB
- **Generación PDF:** DomPDF
- **Gestión Excel:** Laravel Excel (Maatwebsite)
- **Seguridad:** Spatie Laravel Permission

---

## 🚀 Instalación Paso a Paso

### Requisitos Previos
- **PHP >= 8.2**
- **Composer**
- **MySQL / MariaDB**
- **Node.js & NPM**

### Pasos de Configuración

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/HIDEKI7W7/Punto-de-Venta.git
   cd Punto-de-Venta
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configurar el entorno:**
   - Copia el archivo `.env.example` a `.env`:
   ```bash
   cp .env.example .env
   ```
   - Configura tus credenciales de base de datos en el archivo `.env`.

4. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Instalar dependencias de Frontend:**
   ```bash
   npm install && npm run dev
   ```

7. **Crear enlace simbólico:**
   ```bash
   php artisan storage:link
   ```

8. **Iniciar servidor:**
   ```bash
   php artisan serve
   ```

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Siéntete libre de usarlo, modificarlo y distribuirlo.

---

<div align="center">
  <sub>Desarrollado con ❤️ por <a href="https://github.com/HIDEKI7W7">HIDEKI7W7</a></sub>
</div>
