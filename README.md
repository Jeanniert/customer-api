<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



# APIS-CUSTOMERS
Apis de clientes es un proyecto que te permite gestionar tus clientes de forma fácil y segura. Lo creé como parte de mi aprendizaje de Laravel un popular framework de PHP.

## Requisitos
Para instalar y usar este proyecto, necesitas tener lo siguiente:

- PHP 8.0 o superior
- Laravel 11.x o superior
- Composer
- POSTGRESQL o cualquier otro sistema de gestión de bases de datos compatible con Laravel

Puedes consultar la [documentación oficial de Laravel](^1^) para más información sobre cómo instalar y configurar Laravel.

## Instalación
Para instalar este proyecto en tu entorno local, sigue estos pasos:

- Clona este repositorio:    `git clone https://github.com/Jeanniert/customer-api.git`

- abrimos el proyecto:     `cd customer-api`

- Instala las dependencias:    `composer install`

- Crea un archivo .env y copia el contenido del archivo .env.example:    `cp .env.example .env`

- Genera la clave de la aplicación:    `php artisan key:generate`

- Configura la conexión a la base de datos en el archivo .env, indicando el nombre, el usuario, la contraseña y el puerto de tu base de datos.

- Migra la base de datos:    `php artisan migrate`

- Ejecuta el servidor:    `php artisan serve`

- Abre http://localhost:8000 o en su defecto http://127.0.0.1:8000


## Documentacion de la Apis:
 Tambien puedes consultar los endpoints de la apis ejecutando el servidor `php artisan serve` y accediendo a la url http://127.0.0.1:8000/api/docs o en su defecto http://localhost:8000/api/docs  ya que esta Apis cuenta con Swagger que es un conjunto de herramientas de software de código abierto que nos permiten diseñar, construir, documentar y utilizar nuestros servicios web RESTful. 

### Auth

| Method   | URL                                      | Description                              |
| -------- | ---------------------------------------- | ---------------------------------------- |
| `POST`   | `/api/auth/login`                        | Sign in.                                 |
| `POST`   | `/api/auth/register`                     | Register User.                           |
| `GET`    | `/api/logout`                           | Sign in (you must be logged in to use this endpoint.).                    |



### Regions

| Method   | URL                                      | Description                              |
| -------- | ---------------------------------------- | ---------------------------------------- |
| `GET`    | `/api/v1/regions`                           | Retrieve all region.                    |
| `POST`   | `/api/v1/regions`                             | Create a new region.                    |
| `PUT`    | `/api/v1/regions/{id}`                        | Update data region.                     |
| `DELETE` | `/api/v1/regions/{id}`                        | Delete region .                    |



### Communes

| Method   | URL                                      | Description                              |
| -------- | ---------------------------------------- | ---------------------------------------- |
| `GET`    | `/api/v1/communes`                           | Retrieve all commune.                    |
| `POST`   | `/api/v1/communes`                             | Create a new commune.                    |
| `PUT`    | `/api/v1/communes/{id}`                        | Update data commune.                     |
| `DELETE` | `/api/v1/communes/{id}`                        | Delete commune .                    |


### Customers

| Method   | URL                                      | Description                              |
| -------- | ---------------------------------------- | ---------------------------------------- |
| `GET`    | `/api/v1/customer`                           | Retrieve all customer.                    |
| `POST`   | `/api/v1/customer`                             | Create a new customer.                    |
| `PUT`    | `/api/v1/customer/{id}`                        | Update data customer.                     |
| `DELETE` | `/api/v1/customer/{id}`                        | Delete customer .                    |
