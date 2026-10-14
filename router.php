<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['centro'])) $_SESSION['centro'] = 'C01';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/asignar') {
  $centro = $_GET['centro'] ?? '';
  if (!in_array($centro, ['C01','C02'], true)) { http_response_code(400); exit('Centro inválido'); }
  $_SESSION['centro'] = $centro;
  header('Location: /panel'); exit;
}
if ($path === '/panel') {
  header('Content-Type: text/html; charset=utf-8');
  $actual = htmlspecialchars($_SESSION['centro'], ENT_QUOTES);
  echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>EQ-09</title><body style="font:18px system-ui;max-width:780px;margin:60px auto"><h1>EQ-09 · Comprobación de acceso</h1><p>Cuenta ficticia P10 (administración) · Centro asignado: <strong>'.$actual.'</strong></p><p><a href="/asignar?centro=C01">1. Asignar C01</a> | <a href="/asignar?centro=C02">2. Retirar C01 y asignar C02</a></p><p><a href="/centros/C01/configuracion" target="_blank">Abrir GET /centros/C01/configuracion</a></p><p>La petición es siempre la misma. Abre el inspector de red (Desarrollo → Mostrar inspector web → Red), recarga y selecciona «configuracion» para ver HTTP 200 o 403.</p></body></html>';
  exit;
}
if (preg_match('~^/centros/(C01|C02)/configuracion$~', $path, $matches)) {
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  $requested = $matches[1];
  $user = ['activa'=>true,'rol'=>'administracion','centro'=>$_SESSION['centro']];
  if (!$user['activa'] || $user['rol'] !== 'administracion' || $requested !== $user['centro']) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>['codigo'=>'SIN_PERMISO','mensaje'=>'No tienes permiso para esta operación.']],JSON_UNESCAPED_UNICODE); exit;
  }
  http_response_code(200);
  echo json_encode(['ok'=>true,'data'=>['codigo'=>$requested,'nombre'=>$requested==='C01'?'Centro Norte':'Centro Sur']],JSON_UNESCAPED_UNICODE); exit;
}
http_response_code(404); echo 'Ruta no encontrada';
