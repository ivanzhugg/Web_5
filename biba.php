<?php
session_start();
require_once("db.php");

$login = trim($_POST['login']);
$password = trim($_POST['password']);


$stmt = $conn->prepare("SELECT id, hash FROM users WHERE id = ?");
$stmt->bind_param("i", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: edit.php");
        exit();
    } else {
        echo "Неверный пароль. <a href='index.html'>Попробовать снова</a>";
        exit();
    }
} else {
    echo "Пользователь не найден. <a href='index.html'>Попробовать снова</a>";
    exit();
}
?>
