<?php
session_start();
require_once __DIR__ . '/../Class/db.php';
require_once __DIR__ . '/../Class/users.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Необхідно увійти']);
    exit;
}

if (!isset($_POST['recipe_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID рецепту не вказано']);
    exit;
}

$user = new User();
$recipeId = (int)$_POST['recipe_id'];
$userId = $_SESSION['user_id'];

try {
    if ($user->isFavorite($userId, $recipeId)) {
        $user->removeFavorite($userId, $recipeId);
        echo json_encode(['success' => true, 'is_favorite' => false, 'icon' => '🤍']);
    } else {
        $user->addFavorite($userId, $recipeId);
        echo json_encode(['success' => true, 'is_favorite' => true, 'icon' => '❤️']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}