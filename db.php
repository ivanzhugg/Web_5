<?php
$servername = "localhost";
$username = "root";
$password = "kraga228";
$dbname = "php_home3";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    error_log("Connection failed: " . mysqli_connect_error());
    die("Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.");
}
?>
