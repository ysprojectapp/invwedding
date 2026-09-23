<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'message'=>'Method tidak diizinkan']); exit; }
$input = json_decode((string)file_get_contents('php://input'), true) ?: $_POST;
$invitation = clean_guest((string)($input['invitation'] ?? '')); $name = clean_guest((string)($input['name'] ?? '')); $message = trim(mb_substr(clean_guest((string)($input['message'] ?? '')), 0, 500));
if (!$invitation || !$name || mb_strlen($message) < 3) { http_response_code(422); echo json_encode(['ok'=>false,'message'=>'Nama dan ucapan wajib diisi.']); exit; }
try { $q=db()->prepare('SELECT id FROM invitations WHERE slug=? AND status="active" LIMIT 1'); $q->execute([$invitation]); $id=(int)$q->fetchColumn(); if(!$id) throw new RuntimeException(); $q=db()->prepare('INSERT INTO wishes(invitation_id,name,message,status) VALUES(?,?,?,"pending")'); $q->execute([$id,$name,$message]); echo json_encode(['ok'=>true,'message'=>'Ucapan menunggu persetujuan admin.']); } catch(Throwable) { http_response_code(500); echo json_encode(['ok'=>false,'message'=>'Ucapan belum dapat disimpan.']); }
