<?php
$data_file = 'data.txt';

function load_messages($file) {
    $messages = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            list($name, $email, $timestamp, $message) = explode('|', $line);
            $message = str_replace('[NEWLINE]', "\n", $message);
            $messages[] = [
                'name' => $name,
                'email' => $email,
                'timestamp' => $timestamp,
                'message' => nl2br(htmlspecialchars($message))
            ];
        }
    }
    return array_reverse($messages);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $timestamp = date('Y-m-d H:i:s');
        $message = str_replace("\n", '[NEWLINE]', $message); 
        $entry = "$name|$email|$timestamp|$message\n";
        file_put_contents($data_file, $entry, FILE_APPEND);
        header('Location: index.php');
        exit;
    }
}

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
