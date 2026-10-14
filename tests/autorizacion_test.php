<?php
declare(strict_types=1);
$port = 8765;
$cmd = escapeshellarg(PHP_BINARY).' -S 127.0.0.1:'.$port.' '.escapeshellarg(dirname(__DIR__).'/router.php');
$proc = proc_open($cmd, [['pipe','r'],['file',sys_get_temp_dir().'/eq09_server.log','a'],['file',sys_get_temp_dir().'/eq09_server.log','a']],$pipes,dirname(__DIR__));
if (!is_resource($proc)) {fwrite(STDERR,"No se pudo arrancar servidor\n");exit(1);}
try {
  usleep(450000);
  $cookie = tempnam(sys_get_temp_dir(),'eq09');
  $request = function(string $path) use($port,$cookie):array {
    $ch=curl_init('http://127.0.0.1:'.$port.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie,CURLOPT_FOLLOWLOCATION=>true]);
    $body=curl_exec($ch);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);$err=curl_error($ch);curl_close($ch);
    if($body===false) throw new RuntimeException($err);
    return [$status,$body];
  };
  $cases=[['C01',200],['C02',403],['C01',200]];
  foreach($cases as $i=>[$centro,$expected]){
    $request('/asignar?centro='.$centro);
    [$status,$body]=$request('/personal/usuarios?centro=C01');
    $json=json_decode($body,true);
    $ok=$status===$expected && is_array($json) && (($expected===403 && !array_key_exists('data',$json)) || ($expected===200 && ($json['data']['centro']??'')==='C01'));
    echo 'Prueba '.($i+1).' (asignado '.$centro.'): HTTP '.$status.' '.($ok?'OK':'FALLO').PHP_EOL;
    if(!$ok) exit(1);
  }
  echo "3/3 pruebas superadas\n";
} finally { proc_terminate($proc);proc_close($proc);if(isset($cookie))@unlink($cookie); }
