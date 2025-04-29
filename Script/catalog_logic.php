<?php
$min_time = isset($_GET['min_time']) ? (int)$_GET['min_time'] : 0;
$max_time = isset($_GET['max_time']) ? (int)$_GET['max_time'] : 240;
$selected_categories = isset($_GET['categories']) ? (array)$_GET['categories'] : [];
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

$filtered_recipes = array_filter($recipes, function($recipe) use ($min_time, $max_time, $selected_categories, $search_query) {
    
    if ($recipe->price < $min_time || $recipe->price > $max_time) {
        return false;
    }
    
   
    if (!empty($selected_categories) && !in_array($recipe->category, $selected_categories)) {
        return false;
    }
    
   
    if ($search_query && stripos($recipe->name, $search_query) === false && stripos($recipe->description, $search_query) === false) {
        return false;
    }
    
    return true;
});


$all_categories = array_unique(array_column($recipes, 'category'));
?>

<?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['min_time']) || isset($_GET['categories']) || isset($_GET['search']))): ?>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 1024) {
                document.querySelector('.filter-sidebar').classList.add('active');
            }
        });
    </script>
<?php endif; ?>