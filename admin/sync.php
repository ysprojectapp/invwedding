<?php
require_once __DIR__ . '/../includes/sync_templates.php';
admin_required();
$count = 0; $error = '';
try { $count = sync_templates(); } catch (Throwable $exception) { $error = 'Sinkronisasi gagal. Pastikan database sudah tersedia.'; }
$page_title = 'Sync Template'; require __DIR__ . '/includes/header.php'; require __DIR__ . '/includes/sidebar.php';
?><header class="admin-header"><span class="eyebrow">SYSTEM</span><h1>Sinkronisasi katalog</h1></header><section class="form-card"><p><?= e($error ?: $count . ' template berhasil disinkronkan dari folder JSON.') ?></p><a class="button" href="/admin/templates">Kembali ke Template</a></section><?php require __DIR__ . '/includes/footer.php'; ?>
