<?php
session_start();
require_once 'Class/db.php';
require_once 'Class/recipes.php';
require_once 'Class/pages.php';
require_once 'Class/Logger.php';

$startTime = microtime(true);

$recipeManager = new Recipe();
$recipes = $recipeManager->getAllRecipes();
$categories = $recipeManager->getCategories();

$user = null;
if (isset($_SESSION['user_id'])) {
    require_once 'Class/users.php';
    $user = new User();
}

$page = new MainPage($recipes, $categories, $user);
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter();

// Log the request
$logger = Logger::getInstance();
$responseTime = round((microtime(true) - $startTime) * 1000); // Convert to milliseconds
$logger->logRequest(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT'],
    $responseTime,
    http_response_code()
);

// Log user activity if logged in
if (isset($_SESSION['user_id'])) {
    $logger->logUserActivity(
        $_SESSION['user_id'],
        'VIEW_PAGE',
        'Viewed homepage',
        $_SERVER['REMOTE_ADDR']
    );
}
?>