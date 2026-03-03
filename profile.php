<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

// Теперь этот ID взят из колонки ID_personal вашей таблицы
$personalID = $_SESSION['user_id']; 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
</head>
<body>
    <h2>Авторизация успешна!</h2>
    <p>Ваш ID_personal: <strong><?php echo $personalID; ?></strong></p>
    
    <a href="logout.php">Выйти из системы</a>
</body>
</html>