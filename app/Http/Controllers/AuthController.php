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
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

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
        if (!$request->id_token) {
            return response()->json([
                'success' => false,
            ]);
        }

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

    public function loginWithApple(Request $request)
    {
        Log::info('========== REQUEST ==========');
        Log::info(json_encode($request->all()));
        Log::info('========== REQUEST ==========');

        $teamId = env('APPLE_TEAM_ID');
        $keyId = env('APPLE_KEY_ID');
        $sub = env('APPLE_IOS_SERVICE_ID');
        $aud = 'https://appleid.apple.com'; // it's a fixed URL value
        $iat = strtotime('now');
        $exp = strtotime('+60days');
        $code = $request->code;
        $firstName = $request->first_name;
        $lastName = $request->last_name;

        $client_secret = $this->getClientSecret($teamId, $iat, $exp, $aud, $sub, $keyId);
        $firsResponse = $this->callApple($code, $client_secret);
        if (isset($firsResponse['error'])) {
            return response()->json([
                'success' => false,
                'message' => 'Login fails, please try again',
            ]);
        }
        $secondResponse = $this->callAppleRefreshToken($firsResponse['refresh_token'], $client_secret);
        if (isset($secondResponse['error'])) {
            return response()->json([
                'success' => false,
                'message' => 'Login fails, please try again',
            ]);
        }

        try {
            $payload = $this->getPayload($secondResponse['id_token']);

            if ($payload) {
                $fullName = null;
                if ($firstName && $lastName) {
                    $fullName = $firstName . ' ' . $lastName;
                }
                $email = $payload->email;

                $user = User::where('email', $email)->first();
                if (!$user && $fullName) {
                    $nickname = $this->createNickName($fullName);
                    $user = User::create([
                        'name' => $fullName,
                        'nickname' => $nickname,
                        'email' => $email,
                        'is_fully_set' => false,
                        'premium' => false,
                        'matches_created' => 0,
                        'password' => Hash::make($email)
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

    /**
     * @param $teamId
     * @param int $iat
     * @param int $exp
     * @param string $aud
     * @param $sub
     * @param $keyId
     * @return false|string
     */
    protected function getClientSecret($teamId, int $iat, int $exp, string $aud, $sub, $keyId)
    {
        $keyContent = file_get_contents(base_path() . '/AuthKey_SignIn.p8');
        return JWT::encode([
            'iss' => $teamId,
            'iat' => $iat,
            'exp' => $exp,
            'aud' => $aud,
            'sub' => $sub,
        ], $keyContent, 'ES256', $keyId);
    }

    protected function callApple($code, $client_secret)
    {


        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://appleid.apple.com/auth/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code='.$code.'&redirect_uri=https%3A%2F%2F4a1a558b3ff6.ngrok.io%2Fapi%2Flogin-with-apple&client_id='.env('APPLE_IOS_SERVICE_ID').'&client_secret='.$client_secret,
                CURLOPT_HTTPHEADER => array(
                    'Fcm-Token: f09Jwl2WSaumuWIv9coWJp:APA91bHuHy8gK_LIvYbP_lprEmU6_6CA0P3dCLpszv7WpYnC_gqREg1pUXjhYXDR6I71RBQYKNlrsfiYrcB95GUi3eW9KUqnY_jQei',
                    'Content-Type: application/x-www-form-urlencoded',
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    protected function callAppleRefreshToken($refresh_token, $client_secret)
    {


        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://appleid.apple.com/auth/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'grant_type=refresh_token&client_id='.env('APPLE_IOS_SERVICE_ID').'&client_secret='.$client_secret.'&refresh_token='.$refresh_token,
                CURLOPT_HTTPHEADER => array(
                    'Fcm-Token: f09Jwl2WSaumuWIv9coWJp:APA91bHuHy8gK_LIvYbP_lprEmU6_6CA0P3dCLpszv7WpYnC_gqREg1pUXjhYXDR6I71RBQYKNlrsfiYrcB95GUi3eW9KUqnY_jQei',
                    'Content-Type: application/x-www-form-urlencoded',
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    protected function getPayload($idToken)
    {
        $kSet = json_decode('[{"kty":"RSA","kid":"86D88Kf","use":"sig","alg":"RS256","n":"iGaLqP6y-SJCCBq5Hv6pGDbG_SQ11MNjH7rWHcCFYz4hGwHC4lcSurTlV8u3avoVNM8jXevG1Iu1SY11qInqUvjJur--hghr1b56OPJu6H1iKulSxGjEIyDP6c5BdE1uwprYyr4IO9th8fOwCPygjLFrh44XEGbDIFeImwvBAGOhmMB2AD1n1KviyNsH0bEB7phQtiLk-ILjv1bORSRl8AK677-1T8isGfHKXGZ_ZGtStDe7Lu0Ihp8zoUt59kx2o9uWpROkzF56ypresiIl4WprClRCjz8x6cPZXU2qNWhu71TQvUFwvIvbkE1oYaJMb0jcOTmBRZA2QuYw-zHLwQ","e":"AQAB"},{"kty":"RSA","kid":"eXaunmL","use":"sig","alg":"RS256","n":"4dGQ7bQK8LgILOdLsYzfZjkEAoQeVC_aqyc8GC6RX7dq_KvRAQAWPvkam8VQv4GK5T4ogklEKEvj5ISBamdDNq1n52TpxQwI2EqxSk7I9fKPKhRt4F8-2yETlYvye-2s6NeWJim0KBtOVrk0gWvEDgd6WOqJl_yt5WBISvILNyVg1qAAM8JeX6dRPosahRVDjA52G2X-Tip84wqwyRpUlq2ybzcLh3zyhCitBOebiRWDQfG26EH9lTlJhll-p_Dg8vAXxJLIJ4SNLcqgFeZe4OfHLgdzMvxXZJnPp_VgmkcpUdRotazKZumj6dBPcXI_XID4Z4Z3OM1KrZPJNdUhxw","e":"AQAB"},{"kty":"RSA","kid":"YuyXoY","use":"sig","alg":"RS256","n":"1JiU4l3YCeT4o0gVmxGTEK1IXR-Ghdg5Bzka12tzmtdCxU00ChH66aV-4HRBjF1t95IsaeHeDFRgmF0lJbTDTqa6_VZo2hc0zTiUAsGLacN6slePvDcR1IMucQGtPP5tGhIbU-HKabsKOFdD4VQ5PCXifjpN9R-1qOR571BxCAl4u1kUUIePAAJcBcqGRFSI_I1j_jbN3gflK_8ZNmgnPrXA0kZXzj1I7ZHgekGbZoxmDrzYm2zmja1MsE5A_JX7itBYnlR41LOtvLRCNtw7K3EFlbfB6hkPL-Swk5XNGbWZdTROmaTNzJhV-lWT0gGm6V1qWAK2qOZoIDa_3Ud0Gw","e":"AQAB"}]', true);
        $kSet['keys'] = $kSet;
        array_shift($kSet);
        array_shift($kSet);
        array_shift($kSet);
        $token = $idToken;
        return JWT::decode($token, JWK::parseKeySet($kSet), ['RS256']);
    }
}
