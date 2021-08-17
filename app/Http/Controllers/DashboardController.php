<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        $weekStartDate = $today->startOfWeek()->format('Y-m-d H:i:s');
        $weekEndDate = $today->endOfWeek()->format('Y-m-d H:i:s');
        $matches = Match::all();
        $matchesThisWeek = $matches->whereBetween('when_play', [$weekStartDate, $weekEndDate])->count();
        $iosDevices = User::with(['player', 'devices'])->whereHas('devices', function ($query) {
            return $query->where('platform', 'ios');
        });
        $iosThisWeek = $iosDevices->whereHas('devices', function ($query) use ($weekStartDate, $weekEndDate) {
            return $query->whereBetween('created_at', [$weekStartDate, $weekEndDate]);
        })->count();
        $androidDevices = User::with(['player', 'devices'])->whereHas('devices', function ($query) {
            return $query->where('platform', 'android');
        });
        $androidThisWeek = $androidDevices->whereHas('devices', function ($query) use ($weekStartDate, $weekEndDate) {
            return $query->whereBetween('created_at', [$weekStartDate, $weekEndDate]);
        })->count();
        $newUsersThisWeek = User::all()->whereBetween('created_at', [$weekStartDate, $weekEndDate])->count();

        return view('dashboard', [
            'users' => User::all(),
            'newUsersThisWeek' => $newUsersThisWeek,
            'matches' => $matches,
            'matchesThisWeek' => $matchesThisWeek,
            'iosDevices' => $iosDevices,
            'iosThisWeek' => $iosThisWeek,
            'androidDevices' => $androidDevices,
            'androidThisWeek' => $androidThisWeek,
        ]);
    }
}
