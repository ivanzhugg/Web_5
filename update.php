<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];


$fio = trim($_POST['fio']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$date = $_POST['date'];
$gender = $_POST['gender'];
$bio = trim($_POST['bio']);
$languages = $_POST['languages'] ?? [];


$errors = [];
if (empty($fio)) $errors[] = "ФИО обязательно";
if (empty($email)) $errors[] = "Email обязателен";
if (!in_array($gender, ['male', 'female'])) $errors[] = "Пол не выбран";

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
    echo "<a href='edit.php'>Назад</a>";
    exit();
}


$stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, email = ?, date = ?, gender = ?, info = ? WHERE id = ?");
$stmt->bind_param("ssssssi", $fio, $phone, $email, $date, $gender, $bio, $user_id);
$stmt->execute();


$conn->query("DELETE FROM languages_users WHERE user_id = $user_id");


$lang_ids = [
    "C" => 1, "C++" => 2, "JavaScript" => 3, "PHP" => 4, "Python" => 5,
    "Java" => 6, "Pascal" => 7, "Haskell" => 8, "Clojure" => 9,
    "Prolog" => 10, "Scala" => 11, "Go" => 12
];

foreach ($languages as $lang) {
    if (isset($lang_ids[$lang])) {
        $stmt = $conn->prepare("INSERT INTO languages_users (user_id, language_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $lang_ids[$lang]);
        $stmt->execute();
    }
}

echo "<p style='color:green;'>Данные успешно обновлены!</p>";
echo "<a href='edit.php'>Назад к редактированию</a>";
?>
