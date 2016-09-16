<?php

namespace CoolwayFestivales\BackendBundle\Services;

use ZendService\Apple\Apns\Client\Message as Client;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Apns\Response\Message as Response;
use Symfony\Component\DependencyInjection\ContainerInterface;


class APN
{
    private $container;
    private $client;
    private $appId;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->client = new Client();
    }

    public function sendNotification($tokens, $text, $badge, $appId, $sound, $feast)
    {
        $stats = ["total" => count($tokens), "successful" => 0, "failed" => 0];
        if(isset($appId))
        {
            $this->appId = $appId;
            if($feast->getApnSandbox())
                $environment = 1;
            else
                $environment = 0;

            $filePem = null;
            $fileOption1 = "/var/www/vhosts/gravedadprod.mobi/httpdocs/web/uploads/festivals/pem/".$feast->getApnPemFile();
            $fileOption2 = "/var/www/coolway-festivales/web/uploads/festivals/pem/".$feast->getApnPemFile();

            echo "ip->";
            print_r($_SERVER);
            echo "<br>";
            exit();

            if(file_exists($fileOption1))
            {
                $filePem = $fileOption1;

            }
            elseif (file_exists($fileOption2))
                $filePem = $fileOption2;

            if($filePem)
            {
                foreach ($tokens as $token) {
                    $this->client->open($environment, $filePem, $feast->getApnPassPhrase());
                    $response = $this->sendNew($token, $text, $filePem, $environment, $badge,  $sound);
                    $this->client->close();

                    if ($response) {
                        $stats["successful"] += 1;
                    } else {
                        $stats["failed"] += 1;
                    }

                }
            }


        }

        return $stats;

    }

    public function sendNew($deviceToken, $text, $badge,  $sound){
        $message = new Message();
        $message->setId($this->appId);
        $message->setToken($deviceToken);
        $message->setBadge(null);
        $message->setSound(null);
        $message->setAlert($text);
        $response = $this->client->send($message);
        if ($response->getCode() == Response::RESULT_OK) {
            return true;
        } else {
            return false;
        }
    }


    public function send($deviceToken, $message, $filePem, $environment){

        // El password del fichero .pem
        $passphrase = 'Gravedad147';

        $ctx = stream_context_create();
        //Especificamos la ruta al certificado .pem que hemos creado
        stream_context_set_option($ctx, 'ssl', 'local_cert', $filePem);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        if($environment)
            $url = 'ssl://gateway.push.apple.com:2195';
        else
            $url = 'ssl://gateway.sandbox.push.apple.com:2195';

        // Abrimos conexión con APNS
        $fp = stream_socket_client(
            $url, $err,
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
