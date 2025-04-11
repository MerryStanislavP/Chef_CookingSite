<?php 
class Page 
{
    public $title;
    public $styles = [
        'Style/normalize.css',
        'Style/style_head.css',
        'Style/style_catalog.css',
        'Style/style_index.css',
        'Style/style_footer.css',
        'Style/style_recipe.css',
        'Style/style_favorite.css'
    ];

    public function __construct($title) 
    {
        $this->title = $title;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
    }

    public function ShowHeader()
    {
        include "Parts/header.phtml";
    }

    public function ShowContent()
    {
        include "Parts/index.phtml"; 
    }

    public function ShowFooter()
    {
        include "Parts/footer.phtml";
    }
}

class MainPage extends Page 
{
    public function __construct()
    {
        parent::__construct("Кулінарний сайт");
        require_once 'Class/recipes.php';
    }
}

class RecipePage extends Page 
{
    public $recipe;

    public function __construct($recipe)
    {
        parent::__construct($recipe->name);
        $this->recipe = $recipe;
        require_once 'Class/recipes.php';
    }
    
    public function ShowContent()
    {
        include "Parts/recipe.phtml";
    }  
}

class CatalogPage extends Page 
{
    public function __construct()
    {
        parent::__construct("Каталог рецептів");
        require_once 'Class/recipes.php';
    }
    
    public function ShowContent()
    {
        include "Parts/catalog.phtml";
    }   
}

class FavoritePage extends Page 
{
    public function __construct()
    {
        parent::__construct("Обрані рецепти");
        require_once 'Class/recipes.php';
    }
    
    public function ShowContent(){
        include "Parts/favorite.phtml";
    }   
}
?>