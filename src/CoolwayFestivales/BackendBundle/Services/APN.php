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

        foreach ($tokens as $token) {
            $message = new Message();
            $message->setId($this->appId);
            $message->setToken($token);
            $message->setBadge($badge);
            $message->setSound($sound);

            $message->setAlert($text);

            $response = $this->client->send($message);

            if ($response->getCode() == Response::RESULT_OK) {
                $stats["successful"] += 1;
            } else {
                $stats["failed"] += 1;
            }
        }

        $this->client->close();

        return $stats;
    }
}