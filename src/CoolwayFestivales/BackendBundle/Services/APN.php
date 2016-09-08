<?php

namespace CoolwayFestivales\BackendBundle\Services;

use ZendService\Apple\Apns\Client\Message as Client;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Apns\Response\Message as Response;

class APN
{
    private $client;
    private $appId;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendNotification($tokens, $text, $badge, $appId, $sound, $feast)
    {
        $stats = ["total" => count($tokens), "successful" => 0, "failed" => 0];
        if(isset($appId))
        {
            if(isset($appId))
            {
                $this->appId = $appId;
                $this->client->open($feast->getApnSandbox(), '/uploads/festivals/pem/'.$feast->getApnPemFile(), $feast->getApnPassPhrase());

                foreach ($tokens as $token) {
                    $response = $this->send($token, $text);

                    if ($response) {
                        $stats["successful"] += 1;
                    } else {
                        $stats["failed"] += 1;
                    }
                }

                $this->client->close();

            }
        }

        return $stats;

    }


    public function send($deviceToken, $message){

        // El password del fichero .pem
        $passphrase = 'Gravedad147';

        $ctx = stream_context_create();
        //Especificamos la ruta al certificado .pem que hemos creado
        stream_context_set_option($ctx, 'ssl', 'local_cert', '../app/config/PushNotFestSACK.pem');
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
