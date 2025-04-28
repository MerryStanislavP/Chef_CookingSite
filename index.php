<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/recipes.php';
require_once 'Class/pages.php';
require_once 'Class/users.php';

$recipeManager = new Recipe();
$recipes = $recipeManager->getAllRecipes();
$categories = $recipeManager->getCategories();

$user = null;
if (isset($_SESSION['user_id'])) {
    $user = new User();
}

$page = new MainPage($recipes, $categories, $user);
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter();
?>