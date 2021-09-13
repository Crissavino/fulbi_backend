<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Match;
use App\Models\Player;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $weekStartDate = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
        $weekEndDate = Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');

        $matchesThisWeek = Match::whereBetween('when_play', [$weekStartDate, $weekEndDate])->count();
        $iosDevices = Device::where('platform', 'ios');
        $iosThisWeek = Device::where('platform', 'ios')->whereBetween('created_at', [$weekStartDate, $weekEndDate])->count();
        $androidDevices = Device::where('platform', 'android');
        $androidThisWeek = Device::where('platform', 'android')->whereBetween('created_at', [$weekStartDate, $weekEndDate])->count();
        $newUsersThisWeek = User::whereBetween('created_at', [$weekStartDate, $weekEndDate])->count();

        $matchesChartData = $this->getMatchesChartData();
        $iosChartData = $this->getIosChartData();
        $androidChartData = $this->getAndroidChartData();

        list($byCountry, $byProvince, $byCity) = $this->groupUserLocations();

        return view('dashboard', [
            'users' => User::all(),
            'newUsersThisWeek' => $newUsersThisWeek,
            'matches' => Match::all(),
            'matchesThisWeek' => $matchesThisWeek,
            'iosDevices' => $iosDevices,
            'iosThisWeek' => $iosThisWeek,
            'androidDevices' => $androidDevices,
            'androidThisWeek' => $androidThisWeek,
            'matchesChartData' => $matchesChartData,
            'iosChartData' => $iosChartData,
            'androidChartData' => $androidChartData,
            'byCountry' => $byCountry,
            'byProvince' => $byProvince,
            'byCity' => $byCity,
        ]);
    }

    private function getMatchesChartData()
    {
        list($monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday) = $this->getWeekDays();
        // $mondayMatches = Match::whereBetween('created_at', [
        //     $monday, $tuesday
        // ])->count();

        $mondayMatches = Match::whereDate('created_at', $monday)->count();
        $tuesdayMatches = Match::whereDate('created_at', $tuesday)->count();
        $wednesdayMatches = Match::whereDate('created_at', $wednesday)->count();
        $thursdayMatches = Match::whereDate('created_at', $thursday)->count();
        $fridayMatches = Match::whereDate('created_at', $friday)->count();
        $saturdayMatches = Match::whereDate('created_at', $saturday)->count();
        $sundayMatches = Match::whereDate('created_at', $sunday)->count();

        return [
            'monday' => $mondayMatches,
            'tuesday' => $tuesdayMatches,
            'wednesday' => $wednesdayMatches,
            'thursday' => $thursdayMatches,
            'friday' => $fridayMatches,
            'saturday' => $saturdayMatches,
            'sunday' => $sundayMatches,
            'highNumberForChart' => $this->getHighNumberForChart($mondayMatches, $tuesdayMatches, $wednesdayMatches, $thursdayMatches, $fridayMatches, $saturdayMatches, $sundayMatches)
        ];
    }

    private function getIosChartData()
    {
        list($monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday) = $this->getWeekDays();

        $mondayDevices = $this->getDevicePerDayAndPlatform('ios', $monday);
        $tuesdayDevices = $this->getDevicePerDayAndPlatform('ios', $tuesday);
        $wednesdayDevices = $this->getDevicePerDayAndPlatform('ios', $wednesday);
        $thursdayDevices = $this->getDevicePerDayAndPlatform('ios', $thursday);
        $fridayDevices = $this->getDevicePerDayAndPlatform('ios', $friday);
        $saturdayDevices = $this->getDevicePerDayAndPlatform('ios', $saturday);
        $sundayDevices = $this->getDevicePerDayAndPlatform('ios', $sunday);

        return [
            'monday' => $mondayDevices,
            'tuesday' => $tuesdayDevices,
            'wednesday' => $wednesdayDevices,
            'thursday' => $thursdayDevices,
            'friday' => $fridayDevices,
            'saturday' => $saturdayDevices,
            'sunday' => $sundayDevices,
            'highNumberForChart' => $this->getHighNumberForChart($mondayDevices, $tuesdayDevices, $wednesdayDevices, $thursdayDevices, $fridayDevices, $saturdayDevices, $sundayDevices)
        ];
    }

    private function getAndroidChartData()
    {
        list($monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday) = $this->getWeekDays();

        $mondayDevices = $this->getDevicePerDayAndPlatform('android', $monday);
        $tuesdayDevices = $this->getDevicePerDayAndPlatform('android', $tuesday);
        $wednesdayDevices = $this->getDevicePerDayAndPlatform('android', $wednesday);
        $thursdayDevices = $this->getDevicePerDayAndPlatform('android', $thursday);
        $fridayDevices = $this->getDevicePerDayAndPlatform('android', $friday);
        $saturdayDevices = $this->getDevicePerDayAndPlatform('android', $saturday);
        $sundayDevices = $this->getDevicePerDayAndPlatform('android', $sunday);

        return [
            'monday' => $mondayDevices,
            'tuesday' => $tuesdayDevices,
            'wednesday' => $wednesdayDevices,
            'thursday' => $thursdayDevices,
            'friday' => $fridayDevices,
            'saturday' => $saturdayDevices,
            'sunday' => $sundayDevices,
            'highNumberForChart' => $this->getHighNumberForChart($mondayDevices, $tuesdayDevices, $wednesdayDevices, $thursdayDevices, $fridayDevices, $saturdayDevices, $sundayDevices)
        ];
    }

    /**
     * @param string $platform
     * @param Carbon $day
     * @return int
     */
    private function getDevicePerDayAndPlatform(string $platform, Carbon $day): int
    {
        return User::with(['devices'])->whereHas('devices', function ($query) use ($platform) {
            return $query->where('platform', $platform);
        })->whereHas('devices', function ($query) use ($day) {
            return $query->whereDate('created_at', $day);
        })->count();
    }

    /**
     * @param $mondayNumber
     * @param $tuesdayNumber
     * @param $wednesdayNumber
     * @param $thursdayNumber
     * @param $fridayNumber
     * @param $saturdayNumber
     * @param $sundayNumber
     * @return float|int
     */
    private function getHighNumberForChart($mondayNumber, $tuesdayNumber, $wednesdayNumber, $thursdayNumber, $fridayNumber, $saturdayNumber, $sundayNumber)
    {
        $highNumberForChart = max($mondayNumber, $tuesdayNumber, $wednesdayNumber, $thursdayNumber, $fridayNumber, $saturdayNumber, $sundayNumber);
        $highNumberForChart = (int) ceil($highNumberForChart / 10) * 10;
        return $highNumberForChart;
    }

    /**
     * @return array
     */
    private function getWeekDays(): array
    {
        $monday = Carbon::now()->startOfWeek();
        $tuesday = Carbon::now()->startOfWeek()->nextWeekday();
        $wednesday = Carbon::now()->startOfWeek()->nextWeekday()->nextWeekday();
        $thursday = Carbon::now()->startOfWeek()->nextWeekday()->nextWeekday()->nextWeekday();
        $friday = Carbon::now()->startOfWeek()->nextWeekday()->nextWeekday()->nextWeekday()->nextWeekday();
        $saturday = Carbon::now()->startOfWeek()->nextWeekday()->nextWeekday()->nextWeekday()->nextWeekday()->nextWeekday();
        $sunday = Carbon::now()->startOfWeek()->nextWeekday()->nextWeekday()->nextWeekday()->nextWeekday()->nextWeekday()->nextWeekday();
        return array($monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);
    }

    /**
     * @return array
     */
    private function groupUserLocations(): array
    {
        $byCountry = Player::with(['location'])->get()->pluck('location')->reject(function ($location) {
            return empty($location);
        })->groupBy('country')->sortDesc();
        $byCountry = $byCountry->take(10);

        $byProvince = Player::with(['location'])->get()->pluck('location')->reject(function ($location) {
            return empty($location);
        })->groupBy('province')->sortDesc();
        $byProvince = $byProvince->take(10);

        $byCity = Player::with(['location'])->get()->pluck('location')->reject(function ($location) {
            return empty($location);
        })->groupBy('city')->sortDesc();
        $byCity = $byCity->take(10);

        return array($byCountry, $byProvince, $byCity);
    }
}
