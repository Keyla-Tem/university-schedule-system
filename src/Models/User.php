<?php
namespace App\Models;

class User {
    // Ищем пользователя по email (для проверки при регистрации и входе)
    public static function findByEmail($email) {
        $db = \App\Config\Database::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([trim($email)]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Создаем нового пользователя (Регистрация)
    public static function create($name, $email, $password, $universityId = 1) {
        $db = \App\Config\Database::getDB();
        
        // Хешируем пароль (безопасность — превыше всего, в базу голый пароль не кладем)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $db->prepare("
            INSERT INTO users (university_id, name, email, password_hash) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$universityId, trim($name), trim($email), $passwordHash]);
    }

    public static function findById($id) {
        $db = \App\Config\Database::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function updateProfile($userId, $name, $email, $universityId, $groupId, $password = null) {
        $db = \App\Config\Database::getDB();
        $sql = "UPDATE users SET name = ?, email = ?, university_id = ?, study_group_id = ?";
        $params = [$name, $email, $universityId, $groupId];

        if (!empty($password)) {
            $sql .= ", password_hash = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $userId;
        
        return $db->prepare($sql)->execute($params);
    }
}