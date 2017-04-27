## How to install

Step by step guide on how to get started with this project.

- git clone this repository
- `composer install`
- `cp .env.example .env`
- edit .env with your database credentials
- make sure a key was generated, just run `php artisan key:generate` to make sure
- `php artisan migrate` to create tables in db
- `php artisan db:seed --class=UsersTableSeeder` to fill the table with fake data
- `php artisan serve --port=9999` then visit [http://localhost:9999/](http://localhost:9999/) in your browser

## Example

```sh
$ git clone https://github.com/Salamshaker/laravel-basic-crud.git
$ cd laravel-basic-crud
$ composer update
$ cp .env.example .env
#Edit .env file with database credentials here
$ php artisan key:generate
$ php artisan migrate
$ php artisan db:Seed --class=UsersTableSeeder
$ php artisan serve --port=9999
```
