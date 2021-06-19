<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function editUserPositions(Request $request)
    {
        $user = $request->user();
        $user->player;
        $user->player->positions()->sync($request->positions_ids);

        $user->player->positions;
        $user->player->location;

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'User positions saved',
        ]);
    }

    /**
     * @param $userPositions
     * @param $user
     * @return array|bool[]
     */
    protected function saveUserPositions($userPositions, $user): array
    {
        try {
            foreach ($userPositions as $position => $isThisPosition) {
                $positionFromDB = Position::where('position', $position)->first();
                $user->positions()->detach($positionFromDB->id);
                if ($isThisPosition) {
                    $user->positions()->attach($positionFromDB->id);
                }
            }

            return [
                'success' => true,
            ];
        } catch (\Exception $exception) {
            Log::info('Error during save of user positions', [$exception->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error during save of user positions',
                'error' => $exception->getMessage()
            ];
        }
    }

    public function editUserLocation(Request $request)
    {
        $user = $request->user();
        $userLocationDetails = $request->userLocationDetails;
        if (!$userLocationDetails['country'] || !$userLocationDetails['place_id'] || !$userLocationDetails['formatted_address'] || !$userLocationDetails['country_code'] || !$userLocationDetails['province'] || !$userLocationDetails['province_code'] || !$userLocationDetails['city']) {
            return [
                'success' => false,
                'message' => 'Error during save of user locations',
            ];
        }
        $saveUserLocationResponse = $this->saveUserLocation($user, $userLocationDetails);
        if (!$saveUserLocationResponse['success']) {
            return response()->json([
                'success' => $saveUserLocationResponse['success'],
                'message' => $saveUserLocationResponse['message'],
                'error' => $saveUserLocationResponse['error']
            ]);
        }
        $user->player->location;

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'User location saved',
        ]);
    }

    /**
     * @param $user
     * @param $userLocationDetails
     * @return array|bool[]
     */
    protected function saveUserLocation($user, $userLocationDetails): array
    {
        try {
            if ($user->location) {
                $location = Location::find($user->location->id)->update([
                    'lat' => $userLocationDetails['lat'],
                    'lng' => $userLocationDetails['lng'],
                    'country' => $userLocationDetails['country'],
                    'country_code' => $userLocationDetails['country_code'],
                    'province' => $userLocationDetails['province'],
                    'province_code' => $userLocationDetails['province_code'],
                    'city' => $userLocationDetails['city'],
                    'place_id' => $userLocationDetails['place_id'],
                    'formatted_address' => $userLocationDetails['formatted_address'],
                ]);
            } else {
                $location = Location::create([
                    'lat' => $userLocationDetails['lat'],
                    'lng' => $userLocationDetails['lng'],
                    'country' => $userLocationDetails['country'],
                    'country_code' => $userLocationDetails['country_code'],
                    'province' => $userLocationDetails['province'],
                    'province_code' => $userLocationDetails['province_code'],
                    'city' => $userLocationDetails['city'],
                    'place_id' => $userLocationDetails['place_id'],
                    'formatted_address' => $userLocationDetails['formatted_address'],
                ]);
            }

            $user->player()->update([
                'location_id' => $location->id
            ]);

            return [
                'success' => true,
            ];
        } catch (\Exception $exception) {
            Log::info('Error during save of user location', [$exception->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error during save of user location',
                'error' => $exception->getMessage()
            ];
        }
    }

    public function getUserData(Request $request)
    {
        $user = User::find($request->user_id);
        $user->player->positions;
        $user->player->location;

        Log::info('entra');

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function getUserOffers(Request $request)
    {
        $user = $request->user();

        $users = User::whereHas('player.positions')->where('id', '!=', $user->id)
            ->where('genre_id', $request->genre_id)->with('player.positions');
        Log::info('Players');
        Log::info(json_encode($users->get()->values()));
        if ($users->count() === 0) {
            return response()->json([
                'success' => true,
                'users' => [],
            ]);
        }

        $positionsIds = $request->positions_ids;
        $users = $users
            ->whereHas('player', function(Builder $query) use ($positionsIds) {
                $query->whereHas('positions', function(Builder $query2) use ($positionsIds) {
                    $query2->whereIn('positions.id', $positionsIds);
                });
            });
        Log::info('Players');
        Log::info(json_encode($users->get()->values()));
        if ($users->count() === 0) {
            return response()->json([
                'success' => true,
                'users' => [],
            ]);
        }

        $gr_circle_radius = 6371;
        $max_distance = $request->range;
        $userLat = $user->player->location->lat;
        $userLng = $user->player->location->lng;
        $distance_select = sprintf(
            "
                                    ( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( lat ) ) " .
            " * cos( radians( lng ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( lat ) ) " .
            " ) " .
            ")
                                     ",
            $gr_circle_radius,
            $userLat,
            $userLng,
            $userLat
        );
        $locations = Location::select('*')
            ->having(DB::raw($distance_select), '<=', $max_distance)
            ->get();
        $users = $users->whereHas('player', function(Builder $query) use ($locations) {
            $query->whereIn('players.location_id', $locations->pluck('id'));
        })->with('player.location');
        Log::info('Players');
        Log::info(json_encode($users->get()->values()));
        if ($users->count() === 0) {
            return response()->json([
                'success' => true,
                'users' => [],
            ]);
        }

        // TODO agregar coincidence para cada caso mas adelante
        Log::info('Players');
        Log::info(json_encode($users->get()->values()));

        return response()->json([
            'success' => true,
            'users' => $users->get()->values(),
        ]);

    }
}
