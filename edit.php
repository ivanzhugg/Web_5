<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT name, phone, email, date, gender, info FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


$languages = [];
$stmt = $conn->prepare("SELECT l.language FROM languages_users lu JOIN languages l ON lu.language_id = l.id WHERE lu.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $languages[] = $row['language'];
}

$all_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala", "Go"];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование</title>
</head>
<body>
<h2>Редактирование данных</h2>

<form method="post" action="update.php">
    <label>ФИО:<br><input type="text" name="fio" value="<?= htmlspecialchars($user['name']) ?>"></label><br><br>
    <label>Телефон:<br><input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"></label><br><br>
    <label>Email:<br><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></label><br><br>
    <label>Дата рождения:<br><input type="date" name="date" value="<?= htmlspecialchars($user['date']) ?>"></label><br><br>
    
    Пол:<br>
    <label><input type="radio" name="gender" value="male" <?= $user['gender'] === 'male' ? 'checked' : '' ?>> Мужской</label><br>
    <label><input type="radio" name="gender" value="female" <?= $user['gender'] === 'female' ? 'checked' : '' ?>> Женский</label><br><br>

    <label>Любимые языки программирования:<br>
        <select name="languages[]" multiple size="5">
            <?php foreach ($all_languages as $lang): ?>
                <option value="<?= $lang ?>" <?= in_array($lang, $languages) ? 'selected' : '' ?>><?= $lang ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Биография:<br><textarea name="bio" rows="4" cols="50"><?= htmlspecialchars($user['info']) ?></textarea></label><br><br>

    <button type="submit">Сохранить изменения</button>
</form>

<form method="post" action="logout.php" style="margin-top: 20px;">
    <button type="submit">Выйти</button>
</form>

</body>
</html>
