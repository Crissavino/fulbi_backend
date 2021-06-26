<?php

namespace App\Http\Controllers;

use App\Models\DaysAvailables;
use App\Models\Location;
use App\Models\Position;
use App\Models\Positions;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        return view('users.index', ['users' => $model->paginate(15)]);
    }

    public function getUserPositions(Request $request)
    {
        $user = User::find($request->user_id);
        $user->positions;

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function getUserDaysAvailable(Request $request)
    {
        $user = User::find($request->user_id);
        $user->daysAvailables;

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function editUserDaysAvailable(Request $request)
    {
        $user = User::find($request->user_id);
        $daysAvailables = $request->daysAvailable;
        if (!$daysAvailables[0] && !$daysAvailables[1] && !$daysAvailables[2] && !$daysAvailables[3] && !$daysAvailables[4] && !$daysAvailables[5] && !$daysAvailables[6]) {
            return response()->json([
                'success' => false,
                'message' => 'Error during save of user days available'
            ]);
        }

        $saveUserDaysAvailablesResponse = $this->saveUserDaysAvailables($daysAvailables, $user);
        if (!$saveUserDaysAvailablesResponse['success']) {
            return response()->json([
                'success' => $saveUserDaysAvailablesResponse['success'],
                'message' => $saveUserDaysAvailablesResponse['message'],
                'error' => $saveUserDaysAvailablesResponse['error']
            ]);
        }
        $user->daysAvailables;


        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'User days available saved'
        ]);
    }

    public function getUserLocation(Request $request)
    {
        $user = User::find($request->user_id);
        $user->location;

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

}
