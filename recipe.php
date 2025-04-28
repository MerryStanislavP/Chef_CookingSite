<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/recipes.php';
require_once 'Class/pages.php';
require_once 'Class/users.php';

$recipeId = $_GET['id'] ?? null;
if (!$recipeId) {
    header("Location: index.php");
    exit;
}

$recipeManager = new Recipe();
$recipe = $recipeManager->getRecipeById($recipeId);

if (!$recipe) {
    header("Location: index.php");
    exit;
}

$ingredients = $recipeManager->getRecipeIngredients($recipeId);
$steps = $recipeManager->getRecipeSteps($recipeId);
$tags = $recipeManager->getRecipeTags($recipeId);

$user = null;
if (isset($_SESSION['user_id'])) {
    $user = new User();
}

$page = new RecipePage($recipe, $ingredients, $steps, $tags, $user);
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter();