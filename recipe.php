<?php
if (!class_exists('Page')) {
    require_once 'Class/pages.php';
    require_once 'Class/recipes.php';
}

$recipeId = $_GET['id'] ?? null;
$allRecipes = getSampleRecipes();
$recipe = null;

foreach ($allRecipes as $r) {
    if ($r->id == $recipeId) {
        $recipe = $r;
        break;
    }
}

if ($recipe) {
    $page = new RecipePage($recipe);
    $page->ShowHeader();
    $page->ShowContent();
    $page->ShowFooter();
} else {
    header("Location: index.php");
    exit;
}