<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{

    const HOURS_PAST_BOOKING_STAY = 3;

    public function getBooking(Request $request, $id)
    {
        Log::info('getBooking');
        $booking = Booking::find($id);
        if ($booking === null) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ]);
        }

        $booking->user;
        $booking->field;
        $booking->type;

        foreach ($booking->field->types as $type) {
            if ($type->id === $booking->type_id) {
                $booking->type->cost = number_format($type->pivot->cost, 2);
            }
        }

        $booking->field->currency;
        $booking->field->location;
        $booking->match;
        if ($booking->match) {
            $booking->match->location;
            $booking->match->have_notifications = $booking->match->players()->where('player_id', $request->user()->player->id)
                ->where('have_notifications', true)->exists();
            $booking->match->cost = number_format($booking->match->cost, 2);
            $booking->match->participants = $booking->match->players()->where('is_confirmed', true)->with(['user'])->get()->pluck('user');

        }

        Log::info(json_encode($booking, JSON_PRETTY_PRINT));
        Log::info('end getBooking');

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    public function getMyBookings(Request $request)
    {
        $bookings = $request->user()->bookings;
        $today = Carbon::now()->subHours(self::HOURS_PAST_BOOKING_STAY);
        $bookings = $bookings->filter(function ($booking) use ($today) {
            $booking->type;
            foreach ($booking->field->types as $type) {
                if ($type->id === $booking->type_id) {
                    $booking->type->cost = number_format($type->pivot->cost, 2);
                }
            }
            return $booking->when > $today->toDateTimeString();
        });

        return response()->json([
            'success' => true,
            'bookings' => $bookings->values()
        ]);
    }
}
