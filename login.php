<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/users.php';
require_once 'Class/pages.php';

$error = '';
$page = new LoginPage($error);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $error = 'Будь ласка, заповніть всі поля';
    } else {
        $user = new User();
        $userData = $user->login($login, $password);
        
        if ($userData) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['name'];
            header("Location: index.php");
            exit;
        } else {
            $error = 'Невірний логін або пароль';
        }
    }
}

$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter(); 