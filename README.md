# Sistema de Control de Inventario y Entregas (invtprot)

Un sistema web ligero y eficiente desarrollado en **PHP** para la gestión de inventario, entrada de insumos y control de entregas. Este proyecto está diseñado para facilitar la administración de recursos y donaciones, permitiendo llevar un registro transparente y organizado de lo que se recibe y lo que se distribuye desde la Fundación FAR.

## 🚀 Características Principales

* **Autenticación de Usuarios:** Acceso seguro al sistema mediante credenciales (`login.php`, `logout.php`, `usuarios.php`).
* **Gestión de Insumos:** Registro, edición y visualización del catálogo de artículos disponibles (`insumos.php`, `agregar.php`, `editar_insumo.php`).
* **Control de Inventario:** Monitoreo en tiempo real de las cantidades y existencias (`inventario.php`, `ver.php`).
* **Registro de Entregas:** Módulo específico para asentar la salida o distribución de recursos (`entregar.php`).
* **Generación de Comprobantes:** Creación automática de tickets para respaldar cada entrega o movimiento realizado (`ticket.php`).

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP
* **Base de Datos:** Relacional (Configurable vía `conexion.php`)
* **Frontend:** HTML, CSS, JavaScript (Nativo)

## ⚙️ Requisitos Previos

Para ejecutar este proyecto en un entorno local, necesitarás:

1. Un servidor web local que soporte PHP (como XAMPP, WAMP, o Laragon).
2. Un gestor de base de datos (por ejemplo, phpMyAdmin o SQL Server Management Studio).

## 📥 Instalación y Uso

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/LeandroReyes004/invtprot.git](https://github.com/LeandroReyes004/invtprot.git)
