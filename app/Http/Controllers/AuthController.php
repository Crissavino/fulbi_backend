<?php

namespace App\Http\Controllers;

use App\Mail\SendRecoveryPasswordEmail;
use App\Models\Device;
use App\Models\Location;
use App\Models\Position;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\Concerns\Has;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AuthController extends Controller
{
    const GOOGLE_CLIENT_ID_WEB = '265189331222-u4u3kp6crqh7odfavru2dnmq7pltj08q.apps.googleusercontent.com';
    const GOOGLE_CLIENT_ID_IOS = '265189331222-qb7uvump0qp6mjg1u11eb2opp7cts18s.apps.googleusercontent.com';
    const GOOGLE_CLIENT_ID_ANDROID = '265189331222-9r086sf5s80tmt0d9tk4b6sjf5pro4lm.apps.googleusercontent.com';

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $nickname = $this->createNickName($validatedData['name']);
        $user = User::create([
            'name' => $validatedData['name'],
            'nickname' => $nickname,
            'email' => $validatedData['email'],
            'is_fully_set' => false,
            'premium' => false,
            'matches_created' => 0,
            'password' => Hash::make($validatedData['password'])
        ]);

        $player = $user->player()->create([
            'user_id' => $user->id
        ]);

        // attach all the positions
        $positionsId = Position::where('sport_id', 1)->get()->pluck('id');
        $player->positions()->attach($positionsId);

        $token = $user->createToken('auth_token')->plainTextToken;

        $fcmToken = $request->header('Fcm-Token');
        $device = Device::updateOrCreate([
            'token'   => $fcmToken
        ],[
            'user_id'     => $user->id,
            'token' => $fcmToken
        ]);

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'fcm_token' => $device->token,
            'token_type' => 'Bearer'
        ]);
    }

    public function createNickName($name)
    {
        $nickName = explode(' ', strtolower($name))[0];
        $nickName .= rand(1, 999);

        $i = 0;
        while(User::whereNickname($nickName)->exists())
        {
            $i++;
            $nickName = explode(' ', strtolower($name))[0];
            $nickName .= rand(1, 999);
        }

        return $nickName;
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        $fcmToken = $request->header('Fcm-Token');
        $device = Device::updateOrCreate([
            'token'   => $fcmToken
        ],[
            'user_id'     => $user->id,
            'token' => $fcmToken
        ]);


        $user->player->positions;
        $user->player->location;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
            'fcm_token' => $device->token,
            'token_type' => 'Bearer'
        ]);
    }

    public function me(Request $request)
    {
        $request->user()->player;
        $request->user()->player->positions;
        $request->user()->player->location;
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        }
        $user->tokens()->delete();

        $headerToken = explode(' ', $request->header('authorization'))[1];
        $token = $user->tokens()->where('token', $headerToken)->first();
        $token->delete();

        // TODO borrar token del device

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);

    }

    public function sendRecoveryPasswordEmail(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->firstOrFail();

            $encryptedId = encrypt($user->id);
            $recoverPasswordUrl = route('recover-password', ['encryptedId' => $encryptedId]);

            $details = [
                'title' => __('general.auth.passwordReset'),
                'body' => __('general.auth.mail.reset.body'),
                'url' => $recoverPasswordUrl,
            ];

            Mail::to($user->email)->send(new SendRecoveryPasswordEmail($details));

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function showRecoverPassword($encryptedId)
    {
        $id = decrypt($encryptedId);
        $user = User::find($id);

        return view('players.recoverPassword', [
            'email' => $user->email,
            'encryptedId' => $encryptedId
        ]);
    }

    public function recoverPassword(Request $request, $encryptedId)
    {
        $id = decrypt($encryptedId);
        $user = User::find($id);

        $validatedData = $request->validate([
            'password' => 'required|confirmed|string|min:6'
        ]);

        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        return redirect()->route('home')->with('message', __('general.auth.passwordChanged'));
    }

    public function existEmail(Request $request)
    {

        $existEmail = User::where('email', $request->email)->exists();

        if ($existEmail) {
            return response()->json([
                'success' => true,
                'message' => 'This email already exists'
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }

    public function completeUserProfile(Request $request)
    {
        $validation = $this->validateCompleteProfile($request);
        if (!$validation['success']) {
            return response()->json([
                'success' => $validation['success'],
                'message' => $validation['message']
            ]);
        }
        // $userPositions = $validation['userPositions'];
        // $daysAvailables = $validation['daysAvailables'];
        $userLocationDetails = $validation['userLocationDetails'];
        $user = User::find($request->user_id);

//        $saveUserPositionsResponse = $this->saveUserPositions($userPositions, $user);
//        if (!$saveUserPositionsResponse['success']) {
//            return response()->json([
//                'success' => $saveUserPositionsResponse['success'],
//                'message' => $saveUserPositionsResponse['message'],
//                'error' => $saveUserPositionsResponse['error']
//            ]);
//        }

        $saveUserLocationResponse = $this->saveUserLocation($user, $userLocationDetails);
        if (!$saveUserLocationResponse['success']) {
            return response()->json([
                'success' => $saveUserLocationResponse['success'],
                'message' => $saveUserLocationResponse['message'],
                'error' => $saveUserLocationResponse['error']
            ]);
        }

//        $saveUserDaysAvailablesResponse = $this->saveUserDaysAvailables($daysAvailables, $user);
//        if (!$saveUserDaysAvailablesResponse['success']) {
//            return response()->json([
//                'success' => $saveUserDaysAvailablesResponse['success'],
//                'message' => $saveUserDaysAvailablesResponse['message'],
//                'error' => $saveUserDaysAvailablesResponse['error']
//            ]);
//        }

        try {
            $user->update([
                'is_fully_set' => 1,
                'genre_id' => $request->genre_id
            ]);

        } catch (\Exception $exception) {
            Log::info('Error during save of user isFullySet', [$exception->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error during save of user isFullySet',
                'error' => $exception->getMessage()
            ]);
        }

        $user->player->location;

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'User fully setted'
        ]);

    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validateCompleteProfile(Request $request)
    {

        $userLocationDetails = $request->userLocationDetails;
        if (!$userLocationDetails['country'] || !$userLocationDetails['place_id'] || !$userLocationDetails['formatted_address'] || !$userLocationDetails['country_code'] || !$userLocationDetails['province'] || !$userLocationDetails['province_code'] || !$userLocationDetails['city']) {
            return [
                'success' => false,
                'message' => 'Error during save of user locations'
            ];
        }

        return [
            'success' => true,
            'userLocationDetails' => $userLocationDetails
        ];
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

    public function loginWithGoogle(Request $request)
    {
//        Log::info('==== $request->id_token ====');
//        Log::info(json_encode($request->id_token));
//        die();
        if (!$request->id_token) {
            return response()->json([
                'success' => false,
            ]);
        }

//        $clientsString = json_encode([self::GOOGLE_CLIENT_ID_ANDROID,self::GOOGLE_CLIENT_ID_IOS, self::GOOGLE_CLIENT_ID_ANDROID]);

//        $client = new Google_Client(['client_id' => $clientsString]);
        $client = new Google_Client();
//        $client->setAccessToken($request->access_token);
//        $client->setClientId(self::GOOGLE_CLIENT_ID_WEB);
//        $client->setClientId(self::GOOGLE_CLIENT_ID_IOS);
//        $client->setClientId(self::GOOGLE_CLIENT_ID_ANDROID);
//        dd($client);

        try {
            $payload = $client->verifyIdToken($request->id_token);
            if ($payload) {
                $name = $payload['name'];
                $email = $payload['email'];
                $image = $payload['picture'];

                $user = User::where('email', $email)->first();
                if (!$user) {
                    $nickname = $this->createNickName($name);
                    $user = User::create([
                        'name' => $name,
                        'nickname' => $nickname,
                        'email' => $email,
                        'is_fully_set' => false,
                        'premium' => false,
                        'matches_created' => 0,
                        'password' => Hash::make($email),
                        'profile_image' => $image
                    ]);

                    $player = $user->player()->create([
                        'user_id' => $user->id
                    ]);

                    // attach all the positions
                    $positionsId = Position::where('sport_id', 1)->get()->pluck('id');
                    $player->positions()->attach($positionsId);

                    $token = $user->createToken('auth_token')->plainTextToken;

                    $fcmToken = $request->header('Fcm-Token');
                    $device = Device::updateOrCreate([
                        'token'   => $fcmToken
                    ],[
                        'user_id'     => $user->id,
                        'token' => $fcmToken
                    ]);

                    $user->player->positions;
                    $user->player->location;

                    return response()->json([
                        'success' => true,
                        'user' => $user,
                        'token' => $token,
                        'fcm_token' => $device->token,
                        'token_type' => 'Bearer'
                    ]);
                }

                $token = $user->createToken('auth_token')->plainTextToken;

                $fcmToken = $request->header('Fcm-Token');
                $device = Device::updateOrCreate([
                    'token'   => $fcmToken
                ],[
                    'user_id'     => $user->id,
                    'token' => $fcmToken
                ]);


                $user->player->positions;
                $user->player->location;

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $user,
                    'fcm_token' => $device->token,
                    'token_type' => 'Bearer'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'empty' => true,
                    'payload' => $payload,
                ]);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }

    }
}
