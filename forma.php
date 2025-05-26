<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="forma" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Form</title>
</head>
<body>

<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pas_data = isset($_COOKIE['pas_data']) ? json_decode($_COOKIE['pas_data'], true) : [];
$data = isset($_COOKIE['form_data']) ? json_decode($_COOKIE['form_data'], true) : [];
$errors = isset($_COOKIE['form_errors']) ? json_decode($_COOKIE['form_errors'], true) : [];
$saved = isset($_COOKIE['form_saved']) ? json_decode($_COOKIE['form_saved'], true) : [];

$values = !empty($data) ? $data : $saved;

if (isset($_GET['success'])) {
    echo "<p class='success'>Данные успешно сохранены</p>";
    echo "<p class='log_info'>Логин: " . htmlspecialchars($pas_data['login']) . "<br>Пароль: " . htmlspecialchars($pas_data['pas']) . "</p>";
}

setcookie("form_data", "", time() - 3600, "/");
setcookie("form_errors", "", time() - 3600, "/");
?>

<div class="forma">
<form action="submit.php" method="POST">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

  <label>ФИО:<br>
    <input type="text" name="fio" value="<?= htmlspecialchars($values['fio'] ?? '') ?>" class="<?= isset($errors['fio']) ? 'error-field' : '' ?>">
    <?php if (isset($errors['fio'])) echo "<div class='error'>" . htmlspecialchars($errors['fio']) . "</div>"; ?>
  </label><br><br>

  <label>Телефон:<br>
    <input type="tel" name="phone" value="<?= htmlspecialchars($values['phone'] ?? '') ?>" class="<?= isset($errors['phone']) ? 'error-field' : '' ?>">
    <?php if (isset($errors['phone'])) echo "<div class='error'>" . htmlspecialchars($errors['phone']) . "</div>"; ?>
  </label><br><br>

  <label>Email:<br>
    <input type="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'error-field' : '' ?>">
    <?php if (isset($errors['email'])) echo "<div class='error'>" . htmlspecialchars($errors['email']) . "</div>"; ?>
  </label><br><br>

  <label>Дата рождения:<br>
    <input type="date" name="date" value="<?= htmlspecialchars($values['date'] ?? '') ?>" class="<?= isset($errors['date']) ? 'error-field' : '' ?>">
    <?php if (isset($errors['date'])) echo "<div class='error'>" . htmlspecialchars($errors['date']) . "</div>"; ?>
  </label><br><br>

  Пол:<br>
  <label><input type="radio" name="gender" value="male" <?= ($values['gender'] ?? '') === 'male' ? 'checked' : '' ?>> Мужской</label><br>
  <label><input type="radio" name="gender" value="female" <?= ($values['gender'] ?? '') === 'female' ? 'checked' : '' ?>> Женский</label><br>
  <?php if (isset($errors['gender'])) echo "<div class='error'>" . htmlspecialchars($errors['gender']) . "</div>"; ?><br>

  <label>Любимые языки программирования:<br>
    <select name="languages[]" multiple size="5" class="<?= isset($errors['languages']) ? 'error-field' : '' ?>">
      <?php
        $options = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala", "Go"];
        $selected = $values['languages'] ?? [];
        foreach ($options as $opt) {
            $isSelected = in_array($opt, $selected) ? "selected" : "";
            echo "<option value=\"" . htmlspecialchars($opt) . "\" $isSelected>" . htmlspecialchars($opt) . "</option>";
        }
      ?>
    </select>
    <?php if (isset($errors['languages'])) echo "<div class='error'>" . htmlspecialchars($errors['languages']) . "</div>"; ?>
  </label><br><br>

  <label>Биография:<br>
    <textarea name="bio" rows="4" cols="50" class="<?= isset($errors['bio']) ? 'error-field' : '' ?>"><?= htmlspecialchars($values['bio'] ?? '') ?></textarea>
    <?php if (isset($errors['bio'])) echo "<div class='error'>" . htmlspecialchars($errors['bio']) . "</div>"; ?>
  </label><br><br>

  <label><input type="checkbox" name="contract" <?= isset($values['contract']) ? "checked" : "" ?>> С контрактом ознакомлен(а)</label>
  <?php if (isset($errors['contract'])) echo "<div class='error'>" . htmlspecialchars($errors['contract']) . "</div>"; ?>
  <br><br>

  <button type="submit">Сохранить</button>
</form>
</div>

</body>
</html>
