<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/recipes.php';
require_once 'Class/users.php';
require_once 'Class/pages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$recipeManager = new Recipe();
$favorites = $user->getFavorites($_SESSION['user_id']);

$page = new FavoritePage($favorites);
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter();
?>