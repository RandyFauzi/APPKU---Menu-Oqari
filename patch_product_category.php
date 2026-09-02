<?php
$content = file_get_contents("app/Models/Product.php");

// Replace category_name with category_id in fillable
$content = str_replace("'category_name',", "'category_id',", $content);

// Add category relationship
$relations = "
    public function category()
    {
        return \$this->belongsTo(Category::class);
    }
";

// Insert before the last closing brace
$content = preg_replace('/(class Product extends Model\s*\{[\s\S]*?)(?=\})/', "$1$relations", $content);

file_put_contents("app/Models/Product.php", $content);
