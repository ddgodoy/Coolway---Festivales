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
    private $fp;

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

            $ip = $this->container->get('request')->getClientIp();

            if($ip == "62.75.210.58")
                $filePem = "/var/www/vhosts/gravedadprod.mobi/httpdocs/web/uploads/festivals/pem/".$feast->getApnPemFile();
            else
                $filePem = "/var/www/coolway-festivales/web/uploads/festivals/pem/".$feast->getApnPemFile();


            $this->client->open($environment, $filePem, $feast->getApnPassPhrase());

            $this->openConnection($environment, $filePem);
            foreach ($tokens as $token) {
                $response = $this->send($token, $text);

                if ($response) {
                    $stats["successful"] += 1;
                } else {
                    $stats["failed"] += 1;
                }
            }
            $this->closeConnection();

        }

        return $stats;

    }


    public function send($deviceToken, $message){

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
        fwrite($this->fp, $msg, strlen($msg));

    }


    public function openConnection($environment, $filePem)
    {
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
        $this->fp = stream_socket_client(
            $url, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$this->fp) {
            return false;
        }
    }

    public function closeConnection()
    {
        fclose($this->fp);
        $this->client->close();
    }
}
