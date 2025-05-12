<?php

spl_autoload_register(function($class) {

    $namespaceMap = [
        'App\\Models'     => __DIR__ . '/Class',
        'App\\Utils'      => __DIR__ . '/Class',
        'App\\Controllers' => __DIR__ . '/Controllers',
        'App\\Views'      => __DIR__ . '/Class',
    ];
    
    foreach ($namespaceMap as $namespace => $dir) {

        if (strpos($class, $namespace) === 0) {

            $relativeClass = substr($class, strlen($namespace) + 1);

            $filePath = $dir . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            
            if (file_exists($filePath)) {
                require_once $filePath;
                return true;
            }
        }
    }
    
    return false;
});

require_once __DIR__ . '/Class/db.php';
require_once __DIR__ . '/Class/users.php';
require_once __DIR__ . '/Class/recipes.php';
require_once __DIR__ . '/Class/Logger.php';
require_once __DIR__ . '/Class/pages.php';

function registerClassAliases() {
    $aliases = [
        'App\\Models\\User' => 'User',
        'App\\Models\\Recipe' => 'Recipe',
        'App\\Models\\Database' => 'Database',
        'App\\Utils\\TextProcessor' => 'TextProcessor',
        'App\\Utils\\Logger' => 'Logger',
        'App\\Views\\Page' => 'Page',
        'App\\Views\\MainPage' => 'MainPage',
        'App\\Views\\RecipePage' => 'RecipePage',
        'App\\Views\\CatalogPage' => 'CatalogPage',
        'App\\Views\\FavoritePage' => 'FavoritePage',
        'App\\Views\\LoginPage' => 'LoginPage',
        'App\\Views\\RegisterPage' => 'RegisterPage',
        'App\\Views\\ProfilePage' => 'ProfilePage'
    ];
    
    foreach ($aliases as $original => $alias) {
        if (class_exists($original) && !class_exists($alias, false)) {
            class_alias($original, $alias);
        }
    }
}

registerClassAliases();