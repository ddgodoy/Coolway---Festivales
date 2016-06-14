<?php

namespace CoolwayFestivales\BackendBundle\Services;

use ZendService\Apple\Apns\Client\Message as Client;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Apns\Response\Message as Response;

class APN
{
    private $client;
    private $appId;

    public function __construct($appId, $pemPath, $sandbox = true, $passPhrase)
    {
        $this->appId = $appId;
        $this->client = new Client();
        $this->client->open($sandbox, $pemPath, $passPhrase);
    }

    public function sendNotification($tokens, $text, $badge, $sound)
    {
        $stats = ["total" => count($tokens), "successful" => 0, "failed" => 0];
        $chunks = array_chunk($tokens, 100);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $token){
                $response = $this->send($token, $text);
    //            $message = new Message();
    //            $message->setId($this->appId);
    //            $message->setToken($token);
    //            $message->setBadge($badge);
    //            $message->setSound($sound);
    //
    //            $message->setAlert($text);
    //
    //            $response = $this->client->send($message);
    //
    //            if ($response->getCode() == Response::RESULT_OK) {
    //                $stats["successful"] += 1;
    //            } else {
    //                $stats["failed"] += 1;
    //            }


                if ($response) {
                    $stats["successful"] += 1;
                } else {
                    $stats["failed"] += 1;
                }
            }
        }

        $this->client->close();

        return $stats;
    }


    public function send($deviceToken, $message){

        // El password del fichero .pem
        $passphrase = 'Gravedad147';

        $ctx = stream_context_create();
        //Especificamos la ruta al certificado .pem que hemos creado
        stream_context_set_option($ctx, 'ssl', 'local_cert', '../app/config/FestPushNotProdCK.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Abrimos conexión con APNS
        $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp) {
            return false;
        }
        
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

        // cerramos la conexión
        fclose($fp);

        if (!$result) {
            return false;
        } else {
            return true;
        }

    }
}
