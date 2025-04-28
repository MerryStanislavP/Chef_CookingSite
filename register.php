<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/users.php';
require_once 'Class/pages.php';

$error = '';

$page = new RegisterPage($error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($login) || empty($password) || empty($confirmPassword)) {
        $error = 'Будь ласка, заповніть всі поля';
    } elseif ($password !== $confirmPassword) {
        $error = 'Паролі не співпадають';
    } else {
        $user = new User();
        
        if ($user->isLoginExists($login)) {
            $error = 'Користувач з таким логіном вже існує';
        } else {
            try {
                $user->register($name, $login, $password);
                header("Location: login.php");
                exit;
            } catch (Exception $e) {
                $error = 'Помилка при реєстрації: ' . $e->getMessage();
            }
        }
    }
}

$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter(); 