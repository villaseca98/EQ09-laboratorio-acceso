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
  echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>EQ-09</title><body style="font:18px system-ui;max-width:750px;margin:60px auto"><h1>EQ-09 · Laboratorio de autorización</h1><p>Cuenta ficticia activa · Rol administración · Centro asignado: <strong>'.$actual.'</strong></p><p><a href="/asignar?centro=C01">Asignar C01</a> | <a href="/asignar?centro=C02">Retirar C01 y asignar C02</a></p><p><a href="/personal/usuarios?centro=C01" target="_blank">Abrir petición GET /personal/usuarios?centro=C01</a></p><p>Para capturas, abre la misma petición después de cada cambio.</p></body></html>';
  exit;
}
if ($path === '/personal/usuarios') {
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  $requested = $_GET['centro'] ?? '';
  $user = ['activa'=>true,'rol'=>'administracion','centro'=>$_SESSION['centro']];
  if (!$user['activa'] || $user['rol'] !== 'administracion' || $requested !== $user['centro'] || !in_array($requested,['C01','C02'],true)) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>['codigo'=>'SIN_PERMISO','mensaje'=>'Acceso denegado para el centro solicitado']],JSON_UNESCAPED_UNICODE); exit;
  }
  http_response_code(200);
  echo json_encode(['ok'=>true,'data'=>['centro'=>$requested,'vista'=>'Recursos humanos (datos ficticios)']],JSON_UNESCAPED_UNICODE); exit;
}
http_response_code(404); echo 'Ruta no encontrada';
