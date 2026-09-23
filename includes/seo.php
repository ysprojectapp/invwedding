<?php
function seo_head(string $title, string $description = '', string $image = ''): void {
    $description = $description ?: 'Undangan pernikahan digital modern, elegan, dan mudah dibagikan.';
    echo '<title>' . e($title) . '</title><meta name="description" content="' . e($description) . '"><meta name="viewport" content="width=device-width, initial-scale=1"><meta property="og:title" content="' . e($title) . '"><meta property="og:description" content="' . e($description) . '"><meta property="og:type" content="website">';
    if ($image) echo '<meta property="og:image" content="' . e(public_url($image)) . '">';
}
