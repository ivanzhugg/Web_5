<?php

$servername = "localhost";
$username = "root";
$password = "kraga228";
$dbname = "php_home3";



$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn)
{
    die("Error". mysqli_connect_error());
}

else 
{
    echo "GREAT";
}

?>
