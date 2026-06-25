<?php

/**
 * КЛАСС ДЛЯ РАБОТЫ С БАЗОЙ ДАННЫХ (Database.php)
 * * ЧТО ЭТО ЗА ФАЙЛ И ЗАЧЕМ ОН НУЖЕН?
 * Раньше у нас в проекте подключение к базе создавалось в 'config.php' через обычные 
 * переменные. Из-за этого при каждом подключении (require) файлов, код заново стучался 
 * к серверу MySQL, что тратило память. 
 * Этот класс заменяет старый 'config.php' и решает две критические задачи:
 * 1. Безопасность — он автоматически читает доступы (пароли) из скрытого файла .env.
 * 2. Оптимизация — он гарантирует, что за всё время работы сайта к базе создастся 
 * ровно ОДНО подключение, сколько бы раз мы его ни вызывали.
 * * ЧТО ТАКОЕ PDO (PHP Data Objects)?
 * PDO — это встроенный в PHP современный и безопасный инструмент (интерфейс) для 
 * работы с базами данных. 
 * Почему мы используем PDO вместо старого mysqli_query?
 * - Защита от SQL-инъекций (хакерских атак): PDO заставляет использовать подготовленные 
 * запросы (prepared statements), которые аппаратно разделяют SQL-команды от данных.
 * - Ошибки в виде исключений (Exceptions): Если упадет база данных, PDO выдаст понятную 
 * ошибку, которую легко отловить и залогировать.
 * * ЧТО ТАКОЕ ПАТТЕРН SINGLETON (ОДИНОЧКА)?
 * Обрати внимание: у этого класса конструктор приватный (private function __construct). 
 * Это значит, что никто в коде не сможет случайно написать `$db = new Database()`. 
 * Создать объект можно только один раз через метод `getInstance()`.
 * * КАК ЭТИМ ПОЛЬЗОВАТЬСЯ В КОДЕ?
 * Вместо старого `$pdo` теперь в любом файле/модели мы пишем:
 * * use App\Config\Database;
 * * // Получаем безопасное PDO подключение
 * $db = Database::getInstance()->getConnection();
 * * // Делаем запрос (пример с подготовленным запросом)
 * $stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
 * $stmt->execute([$id]);
 * $room = $stmt->fetch();
 */

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    // Храним единственный экземпляр класса 
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    // Приватный конструктор: запрещаем создавать объект через `new Database()`
    private function __construct()
    {
        $this->connect();
    }

    // Запрещаем клонирование объекта
    private function __clone() {}

    // Запрещаем десериализацию объекта
    public function __wakeup()
    {
        throw new RuntimeException("Cannot unserialize a singleton.");
    }

    /**
     * Точка входа для получения экземпляра класса
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Подключение к базе данных на основе параметров из .env
     */
    private function connect(): void
    {
        // Простейший парсер .env (пока у нас нет автозагрузчика Composer)
        // Ищем файл .env в корне проекта (на два уровня выше текущего файла)
        $envPath = dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envPath)) {
            throw new RuntimeException("Environment file (.env) not found at: {$envPath}");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $config = [];
        
        foreach ($lines as $line) {
            // Игнорируем комментарии
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                // Очищаем от кавычек и пробелов
                $config[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }

        // Вытягиваем доступы или ставим дефолтные значения
        $host = $config['DB_HOST'] ?? 'localhost';
        $dbname = $config['DB_NAME'] ?? '';
        $user = $config['DB_USER'] ?? 'root';
        $pass = $config['DB_PASS'] ?? '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Включаем алерты об ошибках
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Массивы возвращаются с именами колонок
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Истинные подготовленные запросы ради безопасности
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Получить чистый объект PDO для выполнения запросов
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Статический шорткат — чтобы модели могли писать Database::getConnection()
     * Внутри просто вызывает getInstance()->getConnection(), singleton сохраняется
     */
    public static function getDB(): PDO
    {
        return self::getInstance()->getConnection();
    }
}