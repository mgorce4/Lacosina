<?php
try
{
    $host = "db";
    $dbname = "lacosina";
    $username = "myuser";
    $password = "monpassword";

    $pdo = new PDO("mysql:host=$host; dbname=$dbname", $username, $password);

}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
?>