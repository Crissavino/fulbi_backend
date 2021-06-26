<?php


namespace App\src\Infrastructure\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class FcmPushNotificationsService
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string[]
     */
    private $headers;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://fcm.googleapis.com',
            'timeout' => 10.0
        ]);

        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('FCM_KEY'),
        ];
    }

    static function sendChatTextMessage($notificationTitle, $notificationBody, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "new_chat_message";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                    //"to" => $deviceToken
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    static function sendMatchInvitation($notificationTitle, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "match_invitation";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        // "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    static function sendRejectMatchInvitation($notificationTitle, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "reject_invitation";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        // "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    static function sendJoinedToMatch($notificationTitle, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "joined_match";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        // "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    static function sendLeftMatch($notificationTitle, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "left_match";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        // "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    static function sendMatchCreated($notificationTitle, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "match_created";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        // "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    static function sendMatchEdited($notificationTitle, $data, $devicesTokens)
    {

        try {
            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'timeout' => 10.0
            ]);
            $data['notification_type'] = "match_edited";
            $response = $client->request('post', '/fcm/send',[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FCM_KEY'),
                ],
                'json' => [
                    'notification' => [
                        "title" => $notificationTitle,
                        // "body" => $notificationBody,
                    ],
                    "priority" => "high",
                    "data" => $data,
                    "registration_ids" => $devicesTokens
                ]
            ]);

            $body = $response->getBody();
            $bodyContent = $body->getContents();
            Log::info("BODY ============");
            Log::info(json_encode($body));
            Log::info("============ BODY");
            Log::info("BODY CONTENT ============");
            Log::info(json_encode($bodyContent));
            Log::info("============ BODY CONTENT");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

}
