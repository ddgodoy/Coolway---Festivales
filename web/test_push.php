<?php
// Nuestro token
$deviceToken = 'b6fbcb21108efc12f6f919c0a9a6ec2246a85177d9b18ec84b6de77d2d534d99';
 
// El password del fichero .pem
$passphrase = 'Gravedad147';
 
// El mensaje push
$message = '¡Mi primer mensaje Push!';
 
$ctx = stream_context_create();
//Especificamos la ruta al certificado .pem que hemos creado
stream_context_set_option($ctx, 'ssl', 'local_cert', '../app/config/CaboDePlataCK.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
 
// Abrimos conexión con APNS
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
 
if (!$fp) {
	exit("Error de conexión: $err $errstr" . PHP_EOL);
}
 
echo 'Conectado al APNS' . PHP_EOL;
 
// Creamos el payload
$body['aps'] = array(
	'alert' =>$message,
	'sound' => 'bingbong.aiff',
	'badge' => 35
	);
 
// Lo codificamos a json
$payload = json_encode($body);
 
// Construimos el mensaje binario
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
 
// Lo enviamos
$result = fwrite($fp, $msg, strlen($msg));
 
if (!$result) {
	echo 'Mensaje no enviado' . PHP_EOL;
} else { 
	echo 'Mensaje enviado correctamente' . PHP_EOL;
}
 
// cerramos la conexión
fclose($fp);