<?php
session_start();
if (!isset($_SESSION['user_id'])) exit;

$pdo = new PDO("mysql:host=localhost;dbname=kvd", "root", "Krossover2007");
$stmt = $pdo->prepare("SELECT file_name, file_data FROM user_files WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if ($file) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
    echo $file['file_data'];
}