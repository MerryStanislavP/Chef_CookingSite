<?php
session_start();
require_once 'autoload.php';

use App\Models\User;
use App\Models\Recipe;
use App\Views\FavoritePage;

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