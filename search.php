<?php
session_start();
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvd;charset=utf8mb4", "root", "Krossover2007");
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = $_POST['login'] ?? '';
        $pass = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT ID_personal FROM users WHERE Login = ? AND Password = ?");
        $stmt->execute([$login, $pass]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['ID_personal'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Неверный логин или пароль']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка БД']);
}