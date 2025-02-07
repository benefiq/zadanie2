<?php
// Имя файла, в котором хранятся сообщения
$data_file = 'data.txt';

// Функция загрузки сообщений из файла
function load_messages($file) {
    $messages = []; // Создаем пустой массив для хранения сообщений
    if (file_exists($file)) { // Проверяем, существует ли файл
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Читаем файл построчно, игнорируя пустые строки
        foreach ($lines as $line) { // Перебираем каждую строку
            list($name, $email, $timestamp, $message) = explode('|', $line); // Разбиваем строку по разделителю "|"
            $message = str_replace('[NEWLINE]', "\n", $message); // Восстанавливаем переводы строк
            $messages[] = [ // Добавляем сообщение в массив
                'name' => $name,
                'email' => $email,
                'timestamp' => $timestamp,
                'message' => nl2br(htmlspecialchars($message)) // Преобразуем символы в HTML-сущности и добавляем переносы строк
            ];
        }
    }
    return array_reverse($messages); // Возвращаем сообщения в обратном порядке (от новых к старым)
}

// Проверяем, был ли запрос методом POST (форма отправлена)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']); // Очищаем имя от лишних пробелов
    $email = trim($_POST['email']); // Очищаем email от лишних пробелов
    $message = trim($_POST['message']); // Очищаем сообщение от лишних пробелов

    // Проверяем, что все поля заполнены
    if (!empty($name) && !empty($email) && !empty($message)) {
        $timestamp = date('Y-m-d H:i:s'); // Получаем текущую дату и время
        $message = str_replace("\n", '[NEWLINE]', $message); // Заменяем переводы строк на специальный маркер
        $entry = "$name|$email|$timestamp|$message\n"; // Формируем строку для записи в файл
        file_put_contents($data_file, $entry, FILE_APPEND); // Добавляем строку в файл

        header('Location: index.php'); // Перенаправляем пользователя на главную страницу
        exit; // Прекращаем выполнение скрипта
    }
}

// Загружаем сообщения из файла
$messages = load_messages($data_file);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Гостевая книга</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .message-header { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Гостевая книга</h1>
    <form method="post">
        <input type="text" name="name" placeholder="Ваше имя" required><br><br>
        <input type="email" name="email" placeholder="Ваш email" required><br><br>
        <textarea name="message" placeholder="Ваше сообщение" required></textarea><br><br>
        <input type="submit" value="Отправить">
    </form>

    <h2>Сообщения</h2>
    <?php foreach ($messages as $msg): ?>
        <div class="message">
            <div class="message-header"><?= htmlspecialchars($msg['name']) ?> (<?= htmlspecialchars($msg['email']) ?>) - <?= htmlspecialchars($msg['timestamp']) ?></div>
            <div><?= $msg['message'] ?></div>
        </div>
    <?php endforeach; ?>
</body>
</html>
