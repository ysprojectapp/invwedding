<?php
$meta = $template['meta'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/security.php'; verify_csrf();
    $name = clean_guest($_POST['name'] ?? ''); $whatsapp = trim($_POST['whatsapp'] ?? ''); $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
    if ($name && preg_match('/^[0-9+() -]{8,20}$/', $whatsapp)) {
        try {
            $pdo = db(); $pdo->beginTransaction();
            $s = $pdo->prepare('INSERT INTO customers(name,whatsapp,email) VALUES(?,?,?)'); $s->execute([$name,$whatsapp,$email]); $customer = (int)$pdo->lastInsertId();
            $number = order_id(); $s = $pdo->prepare('INSERT INTO orders(order_number,customer_id,total) VALUES(?,?,?)'); $s->execute([$number,$customer,(int)$meta['price']]); $order = (int)$pdo->lastInsertId();
            $s = $pdo->prepare('INSERT INTO order_items(order_id,template_id,template_name,price) SELECT ?,id,?,? FROM templates WHERE slug=? LIMIT 1'); $s->execute([$order,$meta['name'],(int)$meta['price'],$meta['slug']]);
            $s = $pdo->prepare('INSERT INTO payments(order_id) VALUES(?)'); $s->execute([$order]); $pdo->commit(); header('Location: /order/' . rawurlencode($number)); exit;
        } catch (Throwable $error) { if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack(); $message = 'Database belum siap atau template belum disinkronkan. Import database.sql terlebih dahulu.'; }
    } else $message = 'Lengkapi nama dan nomor WhatsApp yang valid.';
}
$seo = ['title' => 'Pesan ' . ($meta['name'] ?? 'Template') . ' - ' . APP_NAME, 'description' => 'Checkout undangan pernikahan digital.']; require __DIR__ . '/partials/head.php';
?><main class="section narrow"><span class="eyebrow">CHECKOUT</span><h1>Mulai undanganmu.</h1><p class="muted">Template <strong><?= e($meta['name'] ?? '') ?></strong> · <?= e(rupiah((int)($meta['price'] ?? 0))) ?></p><?php if (!empty($message)): ?><p class="notice"><?= e($message) ?></p><?php endif; ?><form method="post" class="form-card"><?= csrf_field() ?><label>Nama Pemesan<input required maxlength="150" name="name" value="<?= e($_POST['name'] ?? '') ?>"></label><label>Nomor WhatsApp<input required name="whatsapp" inputmode="tel" maxlength="20" value="<?= e($_POST['whatsapp'] ?? '') ?>"></label><label>Email <small>(opsional)</small><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"></label><button class="button" type="submit">Lanjut ke Pembayaran</button></form></main><?php require __DIR__ . '/partials/foot.php'; ?>
