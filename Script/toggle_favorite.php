<?php
session_start();

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipeId = $_POST['recipe_id'] ?? null;
    
    if ($recipeId !== null) {
        if (isset($_SESSION['favorites'][$recipeId])) {
            unset($_SESSION['favorites'][$recipeId]);
            $is_favorite = false;
        } else {
            $_SESSION['favorites'][$recipeId] = true;
            $is_favorite = true;
        }
        
        echo json_encode([
            'success' => true,
            'is_favorite' => $is_favorite
        ]);
        exit;
    }
}

echo json_encode(['success' => false]);