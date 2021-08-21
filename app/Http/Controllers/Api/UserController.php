<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
            'message' => 'User positions saved'
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
                'success' => true
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
        if (!$userLocationDetails['country'] || !$userLocationDetails['formatted_address'] || !$userLocationDetails['province'] || !$userLocationDetails['city']) {
            return [
                'success' => false,
                'message' => 'Error during save of user locations'
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
            'message' => 'User location saved'
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
                    'formatted_address' => $userLocationDetails['formatted_address']
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
                    'formatted_address' => $userLocationDetails['formatted_address']
                ]);
            }

            $user->player()->update([
                'location_id' => $location->id
            ]);

            return [
                'success' => true
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

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function getUserOffers(Request $request)
    {
        $user = $request->user();

        $users = User::whereHas('player.positions')->where('id', '!=', $user->id)
            ->whereIn('genre_id', $request->genres_ids)->with('player.positions');
        if ($users->count() === 0) {
            return response()->json([
                'success' => true,
                'users' => []
            ]);
        }

        $positionsIds = $request->positions_ids;
        $users = $users
            ->whereHas('player', function (Builder $query) use ($positionsIds) {
                $query->whereHas('positions', function (Builder $query2) use ($positionsIds) {
                    $query2->whereIn('positions.id', $positionsIds);
                });
            });

        if ($users->count() === 0) {
            return response()->json([
                'success' => true,
                'users' => []
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
        $users = $users->whereHas('player', function (Builder $query) use ($locations) {
            $query->whereIn('players.location_id', $locations->pluck('id'));
        })->with('player.location');
        if ($users->count() === 0) {
            return response()->json([
                'success' => true,
                'users' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'users' => $users->get()->values()
        ]);

    }

    public function changeNickname(Request $request)
    {
        $user = $request->user();

        if (!User::whereNickname($request->new_nickname)->exists()) {
            $user->update([
                'nickname' => $request->new_nickname
            ]);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'success' => false,
                'messageKey' => 'errors.auth.nicknameTaken'
            ]);
        }

    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function updateProfileImage(Request $request)
    {

        try {
            $user = User::find($request->user_id);

            $newFile = $request->file('profile-image');
            $img = Image::make($newFile->getRealPath());
            $img->fit(300);
            $ext = $newFile->getClientOriginalExtension();
            $fileName = $user->nickname . rand(100000, 999999) . '.' . $ext;
            $userDirectoryPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/profilePictures/' . $user->id;
            if (!file_exists($userDirectoryPath)) {
                mkdir($userDirectoryPath, 0777, true);
            } else {
                $files = array_diff(scandir($userDirectoryPath), array('.','..'));
                foreach ($files as $file) {
                    unlink("$userDirectoryPath/$file");
                }
            }
            $img->save($userDirectoryPath . '/' .$fileName, 60);

            $path = 'https://' . $request->getHost() . '/storage/profilePictures/' . $user->id . '/';
            $user->update([
                'profile_image' => $path . $fileName
            ]);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $exception) {
            Log::info('Line ======== ');
            Log::info($exception->getLine());
            Log::info('======= Line');
            Log::info($exception->getMessage());
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function getAppMinimumVersion()
    {
        return response()->json([
            'success' => true,
            'versionMajor' => 1,
            'versionMinor' => 1,
            'versionPatch' => 26,
            'checkVersion' => false,
        ]);
    }

}
