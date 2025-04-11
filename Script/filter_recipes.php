<?php
require_once __DIR__.'/../Class/recipes.php';
session_start();

$min_time = $_GET['min_time'] ?? 0;
$max_time = $_GET['max_time'] ?? 240;
$selected_categories = $_GET['categories'] ?? [];

$recipes = getSampleRecipes();
$filtered = array_filter($recipes, function($recipe) use ($min_time, $max_time, $selected_categories) {
    $time_ok = $recipe->price >= $min_time && $recipe->price <= $max_time;
    $category_ok = empty($selected_categories) || in_array($recipe->category, $selected_categories);
    return $time_ok && $category_ok;
});

$_SESSION['filtered_recipes'] = $filtered;
header('Location: /index.php');
exit;