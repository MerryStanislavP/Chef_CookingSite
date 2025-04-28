<?php
session_start();
require_once '../Class/recipes.php';

$offset = $_POST['offset'] ?? 0;
$limit = $_POST['limit'] ?? 6;

$recipeManager = new Recipe();
$allRecipes = $recipeManager->getAllRecipes();
$recipes = array_slice($allRecipes, $offset, $limit);
$hasMore = ($offset + $limit) < count($allRecipes);

ob_start();
foreach ($recipes as $recipe): ?>
<div class="body__elem">
    <div class="elem__img">
        <img src="<?= $recipe->image ?>" alt="<?= $recipe->name ?>">
    </div>
    <div class="elem__text">
        <h3><?= $recipe->name ?></h3>
        <p><?= $recipe->description ?></p>
        <p>Час: <?= $recipe->price ?> хв</p>
    </div>
    <div class="elem__actions">
        <button class="favorite-btn" data-id="<?= $recipe->id ?>">
            <?= isset($_SESSION['favorites'][$recipe->id]) ? '❤️' : '🤍' ?>
        </button>
        <a href="recipe.php?id=<?= $recipe->id ?>" class="details-btn">Детальніше</a>
    </div>
</div>
<?php endforeach;

$html = ob_get_clean();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'html' => $html,
    'hasMore' => $hasMore,
    'newOffset' => $offset + $limit
]);
?>