<?php

namespace CoolwayFestivales\BackendBundle\Services;

use Tomloprod\IonicApi\Exception\RequestException;
use Tomloprod\IonicApi\Push;


/**
 * Class IonicPush
 *
 * @package AppBundle\Services
 */
class IonicPush
{


    /**
     * @param $ionicToken
     * @param $ionicProfile
     * @param $tokens
     * @param $title
     * @param $message
     * @param array $payload
     * @return array
     */
    public function sendNotification($ionicToken, $ionicProfile, $deviceTokens, $title, $message, $payload = array())
    {
        $ionicPushApi = new Push($ionicProfile, $ionicToken);


        /**
         * ANDROID [OPTIONAL] CONFIG PARAMETERS
         */

        // Filename of the Icon to display with the notification (string)
        $icon = "icon";

        // Filename or URI of an image file to display with the notification (string)
        $image = "image";

        // Indicates whether each notification message results in a new entry on the notification center on Android.
        // If not set, each request creates a new notification.
        // If set, and a notification with the same tag is already being shown, the new notification replaces the existing one in notification center.
        $tag = "yourTagIfYouNeedIt";

        // When this parameter is set to true, it indicates that the message should not be sent until the device becomes active. (boolean)
        $delayWhileIdle = false;

        // Identifies a group of messages that can be collapsed, so that only the last message gets sent when delivery can be resumed. (string)
        $collapseKey = "group1";


        /**
         * IOS [OPTIONAL] CONFIG PARAMETERS
         */

        // Message Priority. A value of 10 will cause APNS to attempt immediate delivery.
        // A value of 5 will attempt a delivery which is convenient for battery life. (integer)
        $priority = 10;

        // The number to display as the badge of the app icon (integer)
        $badge = 1;

        // Alert Title, only applicable for iWatch devices
        $iWatchTitle = $title;


        // Assign the previously defined configuration parameters to each platform, as well as the title and message:
        $notificationConfig = [
            'title' => $title,
            'message' => $message,
            'android' => [
                'tag' => $tag,
                'icon' => $icon,
                'image' => $image,
                'delay_while_idle' => $delayWhileIdle,
                'collapse_key' => $collapseKey
            ],
            'ios' => [
                'priority' => $priority,
                'badge' => $badge,
                'title' => $iWatchTitle
            ]
        ];

        // [OPTIONAL] You can also pass custom data to the notification. Default => []
        $notificationPayload = $payload;

        // [OPTIONAL] And define, if you need it, a silent notification. Default => false
        $silent = false;

        // [OPTIONAL] Or/and even a scheduled notification for an indicated datetime. Default => ''
        $scheduled = '';
        // [OPTIONAL] Filename of audio file to play when a notification is received. Setting this to default will use the default device notification sound. Default => 'default'
        $sound = 'default';

        // Configure notification:
        $ionicPushApi->notifications->setConfig($notificationConfig, $notificationPayload, $silent, $scheduled, $sound);

        $stats = ["total" => count($deviceTokens), "successful" => 0, "failed" => 0];

        if(count($deviceTokens) > 0){
            $chunks = array_chunk($deviceTokens, 100);

            foreach ($chunks as $tokens) {

                try {
                    // Send notification...
                        $response = $ionicPushApi->notifications->sendNotification($tokens); // ...to some devices
                

                        if($response)
                            $stats['successful'] += count($tokens);
                        else
                            $stats['failed'] += count($tokens);

                    } catch (RequestException $e) {
                        $stats['failed'] += count($tokens);
                    }
            }

            return $stats;
        }else{
            $response = $ionicPushApi->notifications->sendNotificationToAll();
            return $response;
        }

    }
}
