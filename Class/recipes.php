<?php
require_once 'db.php';

class Recipe
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllRecipes() {
        $sql = "SELECT r.*, c.name as category_name 
                FROM recipes r 
                JOIN categories c ON r.category_id = c.id 
                ORDER BY r.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getRecipeById($id) {
        $sql = "SELECT r.*, c.name as category_name 
                FROM recipes r 
                JOIN categories c ON r.category_id = c.id 
                WHERE r.id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }

    public function getRecipeIngredients($recipeId) {
        $sql = "SELECT i.name, ri.quantity, ri.unit, ri.note 
                FROM recipe_ingredients ri 
                JOIN ingredients i ON ri.ingredient_id = i.id 
                WHERE ri.recipe_id = ? 
                ORDER BY i.name";
        return $this->db->query($sql, [$recipeId])->fetchAll();
    }

    public function getRecipeSteps($recipeId) {
        $sql = "SELECT step_no, text, image 
                FROM recipe_steps 
                WHERE recipe_id = ? 
                ORDER BY step_no";
        return $this->db->query($sql, [$recipeId])->fetchAll();
    }

    public function getRecipeTags($recipeId) {
        $sql = "SELECT t.name 
                FROM recipe_tags rt 
                JOIN tags t ON rt.tag_id = t.id 
                WHERE rt.recipe_id = ?";
        return $this->db->query($sql, [$recipeId])->fetchAll(PDO::FETCH_COLUMN);
    }

    public function searchRecipes($query) {
        $sql = "SELECT r.*, c.name as category_name 
                FROM recipes r 
                JOIN categories c ON r.category_id = c.id 
                WHERE MATCH(r.name, r.description) AGAINST(? IN BOOLEAN MODE)";
        return $this->db->query($sql, [$query])->fetchAll();
    }

    public function getRecipesByCategory($categoryId) {
        $sql = "SELECT r.*, c.name as category_name 
                FROM recipes r 
                JOIN categories c ON r.category_id = c.id 
                WHERE r.category_id = ? 
                ORDER BY r.created_at DESC";
        return $this->db->query($sql, [$categoryId])->fetchAll();
    }

    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        return $this->db->query($sql)->fetchAll();
    }
}
?>

