# Proyecto Symfony 6.4 con JWT

![Symfony](https://img.shields.io/badge/Symfony-6.4-black?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql)
![JWT](https://img.shields.io/badge/JWT-Authentication-red?style=for-the-badge&logo=jsonwebtokens)

Este proyecto utiliza Symfony 6.4 junto con JWT Authentication para la gestión de usuarios y autenticación. 

## Tecnologías Utilizadas
- ![Symfony](https://img.shields.io/badge/-Symfony-black?style=flat-square&logo=symfony) **Framework:** Symfony 6.4
- ![PHP](https://img.shields.io/badge/-PHP-777BB4?style=flat-square&logo=php) **Lenguaje:** PHP 8.1+
- ![JWT](https://img.shields.io/badge/-JWT-red?style=flat-square&logo=jsonwebtokens) **Autenticación:** lexik/jwt-authentication-bundle
- ![MySQL](https://img.shields.io/badge/-MySQL-4479A1?style=flat-square&logo=mysql) **Base de Datos:** MySQL

## Instalación

1. Clona el repositorio en tu máquina local:
    ```bash
    git clone https://github.com/gabrielgarcia2211/app-employees.git
    ```

2. Navega al directorio del proyecto:
    ```bash
    cd app-employees/back-end
    ```

3. Instala las dependencias de PHP usando Composer:
    ```bash
    composer install
    ```

4. Ejecuta las migraciones de la base de datos:
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

5. Registra el administrador ejecutando las migraciones específicas:
    ```bash
    php bin/console doctrine:fixtures:load
    ```

6. Inicia el servidor de desarrollo de Symfony:
    ```bash
    symfony server:start
    ```

## Rutas de la API

### Autenticación 🔐
| Método | Endpoint | Descripción |
|--------|---------|-------------|
| `POST` | `/api/login_check` | Iniciar sesión y obtener token JWT |
| `POST` | `/api/register` | Registrar un nuevo usuario |

### Gestión de Empleados 👥
| Método | Endpoint | Descripción |
|--------|---------|-------------|
| `GET` | `/api/employees` | Obtener listado de empleados |
| `POST` | `/api/employees` | Crear un nuevo empleado |
| `PUT` | `/api/employees/{id}/position` | Actualizar el cargo de un empleado |
| `PUT` | `/api/employees/{id}/name` | Actualizar el nombre de un empleado |
| `DELETE` | `/api/employees/{id}` | Eliminar un empleado |

### Lista de Positions 📋
| Método | Endpoint | Descripción |
|--------|---------|-------------|
| `GET` | `/positions` | Obtener listado de posiciones |

## Uso
Para hacer peticiones autenticadas, se debe incluir el token en el encabezado `Authorization`:
```bash
Authorization: Bearer {token}
```