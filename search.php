<?php
session_start();

// Параметры БД
$host = 'localhost';
$db   = 'kvd';
$user = 'root';
$pass = 'Krossover2007';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    // Ищем пользователя по Login и Password (как в вашей таблице)
    $stmt = $pdo->prepare("SELECT ID_personal FROM users WHERE Login = ? AND Password = ?");
    $stmt->execute([$login, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Сохраняем ID_personal в сессию
        $_SESSION['user_id'] = $user['ID_personal'];
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Неверный логин или пароль']);
    }
}