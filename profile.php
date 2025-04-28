<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/users.php';
require_once 'Class/pages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($name)) {
        $error = 'Ім\'я не може бути порожнім';
    } else {
        try {
            if (!empty($currentPassword)) {
                if (empty($newPassword) || empty($confirmPassword)) {
                    $error = 'Будь ласка, введіть новий пароль та підтвердження';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Нові паролі не співпадають';
                } else {
                    $user->updateUser($_SESSION['user_id'], $name, $newPassword);
                }
            } else {
                $user->updateUser($_SESSION['user_id'], $name);
            }
            
            if (empty($error)) {
                header("Location: profile.php");
                exit;
            }
        } catch (Exception $e) {
            $error = 'Помилка при оновленні профілю: ' . $e->getMessage();
        }
    }
}

$page = new ProfilePage($error, $userData);
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter(); 