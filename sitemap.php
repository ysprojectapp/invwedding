<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=utf-8');
$urls = [public_url('/')];
foreach (glob(TEMPLATE_PATH . '/*/template.json') ?: [] as $file) { $data = json_decode((string)file_get_contents($file), true); if (!empty($data['meta']['slug'])) { $urls[] = public_url('template/' . $data['meta']['slug']); $urls[] = public_url('demo/' . $data['meta']['slug']); } }
echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'; foreach ($urls as $url) echo '<url><loc>' . e($url) . '</loc></url>'; echo '</urlset>';
