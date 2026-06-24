<?php

namespace App\Models;

use App\Config\Database;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        // Автоматически внедряет созданное нами чистое PDO соединение-синглтон
        $this->db = Database::getInstance()->getConnection();
    }
}