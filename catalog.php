<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/recipes.php';
require_once 'Class/pages.php';

$recipeManager = new Recipe();
$categoryId = $_GET['category'] ?? null;
$searchQuery = $_GET['search'] ?? null;

if ($searchQuery) {
    $recipes = $recipeManager->searchRecipes($searchQuery);
} elseif ($categoryId) {
    $recipes = $recipeManager->getRecipesByCategory($categoryId);
} else {
    $recipes = $recipeManager->getAllRecipes();
}

$categories = $recipeManager->getCategories();

$user = null;
if (isset($_SESSION['user_id'])) {
    require_once 'Class/users.php';
    $user = new User();
}

$page = new CatalogPage($recipes, $categories, $user);
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter();