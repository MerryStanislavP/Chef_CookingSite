<?php
    $_SESSION['product_items'] = array();
if (!class_exists('Page')) {
    require_once 'Class/pages.php';
}
$page = new CatalogPage();
$page->ShowHeader();
$page->ShowContent();
$page->ShowFooter();