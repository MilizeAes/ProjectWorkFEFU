<?php
session_start();
// Отключаем вывод ошибок в тело ответа, чтобы не ломать JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Сессия истекла']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvd;charset=utf8mb4", "root", "Krossover2007");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Формируем CSV
    $fd = fopen('php://temp', 'r+');
    fprintf($fd, chr(0xEF).chr(0xBB).chr(0xBF)); 
    fputcsv($fd, ['N', 'Activity', 'Unit', 'Value'], ';');
    foreach ($data as $row) {
        fputcsv($fd, [$row['row_index'], $row['activity'], $row['unit'], $row['coeff']], ';');
    }
    rewind($fd);
    $csvContent = stream_get_contents($fd);
    fclose($fd);

    // Сохраняем в таблицу user_files
    $fileName = "report_" . date('Y-m-d_H-i') . ".csv";
    $stmt = $pdo->prepare("INSERT INTO user_files (user_id, file_name, file_data) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $_SESSION['user_id']);
    $stmt->bindParam(2, $fileName);
    $stmt->bindParam(3, $csvContent, PDO::PARAM_LOB);
    $stmt->execute();

    // Очищаем буфер и отправляем чистый успех
    ob_clean(); 
    echo json_encode(['status' => 'success', 'message' => 'Файл успешно сохранен в БД!']);

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;