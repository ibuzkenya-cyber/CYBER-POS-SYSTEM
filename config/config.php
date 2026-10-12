<?php
session_start();
$host='127.0.0.1';
$db='ibuzkenya_pos';
$user='root';
$pass='';
$charset='utf8mb4';
$dsn="mysql:host=$host;dbname=$db;charset=$charset";
$options=[
 PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES=>false
];
try {$pdo=new PDO($dsn,$user,$pass,$options);}
catch(PDOException $e){die("Database connection failed. Import database.sql and check MySQL is running. Error: ".htmlspecialchars($e->getMessage()));}
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function logged_in(){return isset($_SESSION['user']);}
function require_login(){if(!logged_in()){header('Location: index.php');exit;}}
function role(){return $_SESSION['user']['role']??'';}
function money($n){return number_format((float)$n,2);}
