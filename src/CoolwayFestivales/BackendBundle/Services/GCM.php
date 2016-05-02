<?php

namespace CoolwayFestivales\BackendBundle\Services;

use ZendService\Google\Gcm\Client;
use ZendService\Google\Gcm\Message;

class GCM
{
    private $client;

    public function __construct($token)
    {
        $this->client = new Client();
        $this->client->setApiKey($token);

        $httpClient = new \Zend\Http\Client(null, array(
            'adapter' => 'Zend\Http\Client\Adapter\Socket',
            'sslverifypeer' => false
        ));
        $this->client->setHttpClient($httpClient);
    }

    public function sendNotification($tokens, $title, $text, $collapseKey, $packageName, $delay, $ttl, $dry)
    {
        $stats = ["total" => count($tokens), "successful" => 0, "failed" => 0];
        // up to 100 registration ids can be sent to at once
        $chunks = array_chunk($tokens, 100);

        foreach ($chunks as $chunk) {
            $message = new Message();
            $message->setRegistrationIds($chunk);

            // optional fields
            $message->setData(array(
                'title' => $title,
                'message' => $text
            ));
            $message->setCollapseKey($collapseKey);
            $message->setRestrictedPackageName($packageName);
            $message->setDelayWhileIdle($delay);
            $message->setTimeToLive($ttl);
            $message->setDryRun($dry);

            $response = $this->client->send($message);
            $stats["successful"] += $response->getSuccessCount();
            $stats["failed"] += $response->getFailureCount();
        }

        return $stats;
    }
}