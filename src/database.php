<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

$capsule = new Capsule;

// Подключаемся к базе данных внутри Docker-контейнера
$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => 'postgres', // Имя сервиса базы из docker-compose
    'database'  => 'lab_db',
    'username'  => 'lab_user',
    'password'  => 'lab_password',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Тестовая миграция: автоматически создаем таблицу 'users', если её нет в базе
if (!Capsule::schema()->hasTable('users')) {
    Capsule::schema()->create('users', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamps();
    });

    // Сразу добавим одну запись, чтобы проверить чтение из базы
    Capsule::table('users')->insert([
        'name' => 'Иван Иванов',
        'email' => 'ivan@example.com',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
}

// Создаем модель Eloquent для таблицы 'users'
class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
}

