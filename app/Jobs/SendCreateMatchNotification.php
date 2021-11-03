<?php

namespace App\Jobs;

use App\src\Infrastructure\Services\FcmPushNotificationsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class SendCreateMatchNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $matchId;
    private $userDevicesTokensEs;
    private $userDevicesTokensEn;

    /**
     * Create a new job instance.
     *
     * @param $matchId
     * @param $userDevicesTokensEs
     * @param $userDevicesTokensEn
     */
    public function __construct($matchId, $userDevicesTokensEs, $userDevicesTokensEn)
    {
        $this->matchId = $matchId;
        $this->userDevicesTokensEs = $userDevicesTokensEs;
        $this->userDevicesTokensEn = $userDevicesTokensEn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->userDevicesTokensEn)) {
            App::setLocale('en');
            FcmPushNotificationsService::sendMatchCreated(
                __('notifications.match.created'),
                [],
                $this->userDevicesTokensEn
            );

            FcmPushNotificationsService::sendSilence(
                'silence_match_created',
                [
                    'match_id' => $this->matchId
                ],
                $this->userDevicesTokensEn
            );
        }

        if(!empty($this->userDevicesTokensEs)) {
            App::setLocale('es');
            FcmPushNotificationsService::sendMatchCreated(
                __('notifications.match.created'),
                [],
                $this->userDevicesTokensEs
            );

            FcmPushNotificationsService::sendSilence(
                'silence_match_created',
                [
                    'match_id' => $this->matchId
                ],
                $this->userDevicesTokensEs
            );
        }
    }
}
