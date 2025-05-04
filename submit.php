<?php
require_once("db.php");


$fio = trim($_POST["fio"]);
$phone = trim($_POST["phone"]);
$email = trim($_POST["email"]);
$date = $_POST["date"];
$gender = $_POST["gender"] ?? "";
$info = trim($_POST["bio"]);
$languages = $_POST["languages"] ?? [];
$contract = isset($_POST["contract"]) ? "on" : "";

$errors = [];


if (empty($fio) || !preg_match("/^[а-яА-Яa-zA-Z\s\-]+$/u", $fio)) {
    $errors["fio"] = "ФИО может содержать только буквы, пробелы и дефис.";
}
if (empty($phone) || !preg_match("/^(\+7|8)[0-9]{10}$/", preg_replace("/\D/", "", $phone))) {
    $errors["phone"] = "Введите номер телефона в формате +79991234567 или 89991234567.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Некорректный email.";
}
if (empty($date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
    $errors["date"] = "Введите дату в формате ГГГГ-ММ-ДД.";
}
if ($gender !== "male" && $gender !== "female") {
    $errors["gender"] = "Выберите пол.";
}
if (strlen($info) < 10) {
    $errors["bio"] = "Биография должна содержать минимум 10 символов.";
}
if (empty($languages)) {
    $errors["languages"] = "Выберите хотя бы один язык.";
}
if (empty($contract)) {
    $errors["contract"] = "Вы должны согласиться с контрактом.";
}


$data = [
    "fio" => $fio,
    "phone" => $phone,
    "email" => $email,
    "date" => $date,
    "gender" => $gender,
    "bio" => $info,
    "languages" => $languages,
    "contract" => $contract
];
setcookie("form_data", json_encode($data), 0, "/");

if (!empty($errors)) {
    setcookie("form_errors", json_encode($errors), 0, "/");
    header("Location: forma.php");
    exit();
}

function generatePasswordAndHash($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    return [
        'password' => $password,
        'hash' => $hashedPassword
    ];
}

$result = generatePasswordAndHash();
$pas = $result['password'];




$stmt = $conn->prepare("INSERT INTO users (name, phone, email, date, gender, info, hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $fio, $phone, $email, $date, $gender, $info, $result['hash']);
if (!$stmt->execute()) {
    die("Ошибка при добавлении пользователя: " . $stmt->error);
}
$user_id = $stmt->insert_id;
$stmt->close();


foreach ($languages as $lang_name) {
    $lang_name = trim($lang_name);
    $stmt = $conn->prepare("SELECT id FROM languages WHERE language = ?");
    $stmt->bind_param("s", $lang_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $language_id = $result->fetch_assoc()["id"];
    } else {
        $insert_lang = $conn->prepare("INSERT INTO languages (language) VALUES (?)");
        $insert_lang->bind_param("s", $lang_name);
        $insert_lang->execute();
        $language_id = $insert_lang->insert_id;
        $insert_lang->close();
    }
    $stmt->close();
    $link_stmt = $conn->prepare("INSERT INTO languages_users (user_id, language_id) VALUES (?, ?)");
    $link_stmt->bind_param("ii", $user_id, $language_id);
    $link_stmt->execute();
    $link_stmt->close();
}


$login_pas = [
    "login" => $user_id,
    "pas" => $pas
];
setcookie("pas_data", json_encode($login_pas), 0, "/");


setcookie("form_saved", json_encode($data), time() + 365*24*60*60, "/");


setcookie("form_data", "", time() - 3600, "/");
setcookie("form_errors", "", time() - 3600, "/");

header("Location: forma.php?success=1");
exit();
?>
