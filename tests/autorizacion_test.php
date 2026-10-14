<?php
declare(strict_types=1);
$port=8765;
$cmd=escapeshellarg(PHP_BINARY).' -S 127.0.0.1:'.$port.' '.escapeshellarg(dirname(__DIR__).'/router.php');
$proc=proc_open($cmd,[['pipe','r'],['file',sys_get_temp_dir().'/eq09_server.log','a'],['file',sys_get_temp_dir().'/eq09_server.log','a']],$pipes,dirname(__DIR__));
if(!is_resource($proc)){fwrite(STDERR,"No se pudo iniciar el servidor\n");exit(1);}
$cookie=tempnam(sys_get_temp_dir(),'eq09');
try{
 usleep(600000);
 $get=function(string $path)use($port,$cookie):array{
  $ch=curl_init('http://127.0.0.1:'.$port.$path);
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie,CURLOPT_FOLLOWLOCATION=>true]);
  $body=curl_exec($ch);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
  return [$status,json_decode((string)$body,true)];
 };
 foreach([['C01',200],['C02',403],['C01',200]] as $i=>[$centro,$expected]){
  $get('/asignar?centro='.$centro);
  [$status,$json]=$get('/centros/C01/configuracion');
  $ok=$status===$expected && is_array($json) && ($expected===403?!isset($json['data']):($json['data']['codigo']??'')==='C01');
  echo 'Prueba '.($i+1).' · centro '.$centro.' · HTTP '.$status.' · '.($ok?'OK':'FALLO').PHP_EOL;
  if(!$ok)exit(1);
 }
 echo "3/3 pruebas correctas\n";
}finally{proc_terminate($proc);proc_close($proc);@unlink($cookie);}
