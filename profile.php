<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.html'); exit; }

$pdo = new PDO("mysql:host=localhost;dbname=kvd;charset=utf8mb4", "root", "Krossover2007");
$stmt = $pdo->prepare("SELECT Fname, Position FROM users WHERE ID_personal = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
</head>
<body>
    <p>Пользователь: <b><?php echo $user['Fname']; ?></b> | Должность: <?php echo $user['Position']; ?></p>
    <a href="logout.php">Выход</a>
    <hr>
    <table border="1">
        <thead>
            <tr><th>№</th><th>Деятельность</th><th>Ед. изм.</th><th>Коэффициент</th></tr>
        </thead>
        <tbody id="tableBody"></tbody>
    </table>
    <br>
    <button onclick="saveData()">Сохранить отчет в БД</button>
    <a href="download.php">Скачать последний CSV</a>
    <div id="status"></div>

    <script>
        const activities = ["Анализ", "Разработка", "Тесты", "Документация", "Встречи", "Баги", "Бэкап", "ТЗ", "Дизайн", "Код-ревью", "Спринт", "API", "UI/UX", "DevOps", "Support", "Логи", "SQL", "Консультация", "HR", "PR", "Research", "Security", "Refactoring", "Отчет"];
        const tbody = document.getElementById('tableBody');

        activities.forEach((name, i) => {
            tbody.innerHTML += `<tr>
                <td>${i+1}</td>
                <td class="name">${name}</td>
                <td><select class="unit"><option>час</option><option>шт</option></select></td>
                <td><input type="number" class="coeff" value="0"></td>
            </tr>`;
        });

        async function saveData() {
    const rows = document.querySelectorAll('#tableBody tr');
    const payload = Array.from(rows).map(row => ({
        row_index: row.cells[0].innerText,
        activity: row.querySelector('.name').innerText,
        unit: row.querySelector('.unit').value,
        coeff: row.querySelector('.coeff').value
    }));

    const statusDiv = document.getElementById('status');
    statusDiv.innerText = "Сохранение...";
    statusDiv.style.color = "blue";

    try {
        const response = await fetch('save_to_db.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Отображаем успех
            statusDiv.style.color = "green";
            statusDiv.innerText = "✔ " + result.message;

            // Очищаем форму
            rows.forEach(row => {
                row.querySelector('.coeff').value = 0; // Сброс цифр
                row.querySelector('.unit').selectedIndex = 0; // Сброс выпадающего списка
            });
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        statusDiv.style.color = "red";
        statusDiv.innerText = "Ошибка: проверьте консоль (F12)";
        console.error("Ошибка парсинга или сервера:", error);
    }
}
    </script>
</body>
</html>