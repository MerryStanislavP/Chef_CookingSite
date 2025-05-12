<?php
session_start();
require_once 'autoload.php';
require_once 'Class/db.php';
require_once 'Class/users.php';
require_once 'Class/pages.php';

use App\Models\User;
use App\Utils\TextProcessor;
use App\Views\RegisterPage;

$error = '';

$page = new RegisterPage($error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $login = $_POST['login'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($login) || empty($password) || empty($confirmPassword)) {
        $error = 'Будь ласка, заповніть всі поля';
    } elseif (!empty($email) && !TextProcessor::isValidEmail($email)) {
        $error = 'Невірний формат email';
    } elseif (!TextProcessor::isStrongPassword($password)) {
        $error = 'Пароль повинен містити мінімум 8 символів, принаймні одну велику літеру, одну малу літеру та одну цифру';
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
    
    $page = new RegisterPage($error);
}

$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter(); 