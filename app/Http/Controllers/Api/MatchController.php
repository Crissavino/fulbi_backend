<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\src\Infrastructure\Request\StoreOneMatchRequest;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function store(Request $request)
    {

        $requestResponse = (new StoreOneMatchRequest($request))->__invoke();
        if (!$requestResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => __($requestResponse['message']),
            ]);
        }

        return response()->json([
            'success' => true,
            '$requestResponse' => $requestResponse,
        ]);
    }
}
