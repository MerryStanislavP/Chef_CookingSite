<?php 
namespace App\Views;

use App\Models\User;
use App\Models\Recipe;

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
    private $recipes;
    private $categories;
    private $user;

    public function __construct($recipes, $categories, $user = null)
    {
        parent::__construct("Кулінарний сайт");
        $this->recipes = $recipes;
        $this->categories = $categories;
        $this->user = $user;
    }

    public function ShowContent()
    {
        $recipes = $this->recipes;
        $categories = $this->categories;
        $user = $this->user;
        include "Parts/index.phtml";
    }
}

class RecipePage extends Page 
{
    public $recipe;
    public $ingredients;
    public $steps;
    public $tags;
    public $user;

    public function __construct($recipe, $ingredients, $steps, $tags, $user = null)
    {
        parent::__construct($recipe['name']);
        $this->recipe = $recipe;
        $this->ingredients = $ingredients;
        $this->steps = $steps;
        $this->tags = $tags;
        $this->user = $user;
    }
    
    public function ShowContent()
    {
        $recipe = $this->recipe;
        $ingredients = $this->ingredients;
        $steps = $this->steps;
        $tags = $this->tags;
        $user = $this->user;
        include "Parts/recipe.phtml";
    }  
}

class CatalogPage extends Page 
{
    private $recipes;
    private $categories;
    private $user;

    public function __construct($recipes, $categories, $user = null)
    {
        parent::__construct("Каталог рецептів");
        $this->recipes = $recipes;
        $this->categories = $categories;
        $this->user = $user;
    }
    
    public function ShowContent()
    {
        $recipes = $this->recipes;
        $categories = $this->categories;
        $user = $this->user;
        include "Parts/catalog.phtml";
    }   
}

class FavoritePage extends Page 
{
    private $favorites;
    public function __construct($favorites = [])
    {
        parent::__construct("Обрані рецепти");
        $this->favorites = $favorites;
    }
    
    public function ShowContent(){
        $favorites = $this->favorites;
        include "Parts/favorite.phtml";
    }   
}

class LoginPage extends Page {
    private $error;
    public function __construct($error = '') {
        parent::__construct("Увійти");
        $this->error = $error;
    }
    public function ShowContent() {
        $error = $this->error;
        include "Parts/login.phtml";
    }
}

class RegisterPage extends Page {
    private $error;
    public function __construct($error = '') {
        parent::__construct("Реєстрація");
        $this->error = $error;
    }
    public function ShowContent() {
        $error = $this->error;
        include "Parts/register.phtml";
    }
}

class ProfilePage extends Page {
    private $error;
    private $userData;

    public function __construct($error = '', $userData = null) {
        parent::__construct("Профіль");
        $this->error = $error;
        $this->userData = $userData;
    }

    public function ShowContent() {
        $error = $this->error;
        $userData = $this->userData;
        include "Parts/profile.phtml";
    }
}
?>