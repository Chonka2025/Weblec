<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/database.php';

use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;

// 1. Инициализируем Monolog по официальному гайду
$log = new Logger('http_logger');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Level::Info));

// 2. Логируем входящий HTTP-запрос (Метод и URI)
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$log->info("Входящий запрос: [{$requestMethod}] {$requestUri}");

// 3. Получаем данные из PostgreSQL с помощью Eloquent ORM
try {
    $user = User::first(); // Берем первого пользователя из таблицы через модель
    $userData = $user ? "Имя: {$user->name}, Email: {$user->email}" : "Пользователи не найдены";
} catch (\Exception $e) {
    $userData = "Ошибка базы данных: " . $e->getMessage();
    $log->error("Ошибка БД: " . $e->getMessage());
}

// Формируем тело HTTP-ответа (HTML-код страницы)
$responseBody = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Лабораторная работа</title>
</head>
<body>
    <h1>Скелет приложения с Eloquent и Monolog успешно запущен!</h1>
    <p>Данные из базы данных через ORM: <strong>{$userData}</strong></p>
</body>
</html>";

// Выводим страницу в браузер пользователю
echo $responseBody;

// 4. Логируем тело ответа (очищаем от HTML-тегов для читаемости в лог-файле)
$log->info("Тело ответа: " . trim(strip_tags($responseBody)));

