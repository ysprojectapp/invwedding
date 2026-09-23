<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
function sync_templates(): int {
    $pdo=db(); $count=0; $categoryIds=[];
    foreach ($pdo->query('SELECT id,slug FROM template_categories') as $row) $categoryIds[$row['slug']]=(int)$row['id'];
    $sql='INSERT INTO templates(category_id,name,slug,description,price,folder,status,featured,popular,sort_order) VALUES(?,?,?,?,?,?,"published",0,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),price=VALUES(price),category_id=VALUES(category_id),popular=VALUES(popular),sort_order=VALUES(sort_order)';
    foreach(template_catalog() as $data){$meta=$data['meta'];$category=$categoryIds[slugify((string)($meta['category']??''))]??null;$q=$pdo->prepare($sql);$q->execute([$category,$meta['name'],$meta['slug'],$meta['description']??'',(int)($meta['price']??0),$meta['slug'],!empty($meta['popular']),(int)($meta['sort']??99)]);$count++;}
    return $count;
}
