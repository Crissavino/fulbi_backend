<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    //POST https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send HTTP/1.1
    //
    //Content-Type: application/json
    //Authorization: Bearer ya29.ElqKBGN2Ri_Uz...HnS_uNreA
    //
    //{
    //   "message":{
    //      "token":"token_1",
    //      "data":{},
    //      "notification":{
    //        "title":"FCM Message"
    //        "body":"This is an FCM notification message!",
    //      }
    //   }
    //}

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

    public function sendNotification()
    {

        try {
            $response = $this->client->request('post', '/fcm/send',[
                'headers' => $this->headers,
                'json' => [
                    'notification' => [
                        "title" => "Titulo de la noti",
                        "body" => "Text de la notification",
                    ],
                    "priority" => "high",
                    "data" => [
                        "producto" => "Caca"
                    ],
                    "to" => "csXLEIPQSguIVDXEsmTtpD:APA91bGI3CiMKLr8m-V5c5EzzCubwZbOl0AXRtWLsXlEJkMQ41FZj2yPSS1WObFT1_hy52TasxNcfCmUoHKFIvpIUknWejq2Dy0xigGvSjRhC6e5or04sceB62Mewsm4hVhfm1ogaVhZ"
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
