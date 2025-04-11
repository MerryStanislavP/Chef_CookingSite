<?php
class Recipe
{
    public $id;
    public $name;
    public $category;
    public $image;
    public $description;
    public $ingredients;
    public $instructions;
    public $price;

    public function __construct(
        $id,
        $name,
        $category,
        $image,
        $description,
        $ingredients,
        $instructions,
        $price
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->image = $image;
        $this->description = $description;
        $this->ingredients = $ingredients;
        $this->instructions = $instructions;
        $this->price = $price;
    }
}

// Приклад даних рецептів
function getSampleRecipes() {
    return [
        new Recipe(0, "Борщ", "Супи", "Images/borshch.jpg", "Традиційний український борщ", ["буряк", "картопля", "морква"], ["Нарізати овочі", "Варити 30 хвилин"], 120),
        new Recipe(1, "Вареники", "Основні страви", "Images/varenyky.jpg", "Вареники з картоплею", ["тісто", "картопля", "цибуля"], ["Замісити тісто", "Ліпити вареники"], 60),
        new Recipe(2, "Маца", "Випічка", "Images/matza.jpg", "Традиційний єврейський коржик", ["борошно", "вода", "сіль"], ["Змішати інгредієнти", "Тонко розкатати", "Випікати при 200°C 2-3 хвилини"], 15),
        new Recipe(3, "Оладки", "Сніданки", "Images/oladky.jpg", "Млинецькі оладки", ["борошно", "яйця", "молоко"], ["Змішати інгредієнти", "Смажити на пательні"], 30),
        new Recipe(4, "Салат Цезар", "Салати", "Images/cesar.jpg", "Класичний салат Цезар", ["куряче філе", "салат", "крутони"], ["Нарізати інгредієнти", "Змішати"], 45),
        new Recipe(5, "Піца", "Основні страви", "Images/pizza.jpg", "Домашня піца", ["тісто", "томатний соус", "сир"], ["Розкачати тісто", "Випікати 20 хв"], 60),
        new Recipe(6, "Чізкейк", "Десерти", "Images/cheesecake.jpg", "Ніжний чізкейк", ["сир", "печиво", "вершки"], ["Приготувати основу", "Випікати 1 годину"], 180),
        new Recipe(7, "Шаурма", "Фастфуд", "Images/shaurma.jpg", "Домашня шаурма", ["лаваш", "куряче філе", "овочі"], ["Загорнути начинку", "Підсмажити"], 40),
        new Recipe(8, "Гречана каша", "Каші", "Images/grechka.jpg", "Гречка з грибами", ["гречка", "гриби", "цибуля"], ["Варити гречку", "Смажити гриби"], 30)
    ];
}

function getFavoriteRecipes() {
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }
    $allRecipes = getSampleRecipes();
    return array_filter($allRecipes, function($recipe) {
        return isset($_SESSION['favorites'][$recipe->id]);
    });
}
?>