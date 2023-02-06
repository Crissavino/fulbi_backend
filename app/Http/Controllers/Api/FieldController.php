<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Field;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FieldController extends Controller
{
    public function getFieldsOffers(Request $request)
    {
        $fields = Field::all();

        $gr_circle_radius = 6371;
        $max_distance = $request->range;
        $userLat = $request->user()->player->location->lat;
        $userLng = $request->user()->player->location->lng;
        $distance_select = sprintf(
            "( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( lat ) ) " .
            " * cos( radians( lng ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( lat ) ) " .
            " ) " .
            ")",
            $gr_circle_radius,
            $userLat,
            $userLng,
            $userLat
        );
        $locations = Location::select('*')
            ->having(DB::raw($distance_select), '<=', $max_distance)
            ->get();
        $fields = $fields->whereIn('location_id', $locations->pluck('id'));
        if ($fields->count() === 0) {
            return response([
                'success' => true,
                'message' => 'No fields found in range',
                'fields' => []
            ]);
        }

        $fieldTypes = json_decode($request->types, true);
        $fields = $fields->filter(function ($field) use ($fieldTypes) {
            $fieldTypesIds = $field->types->pluck('id')->toArray();
            foreach ($fieldTypes as $fieldType) {
                if (in_array($fieldType, $fieldTypesIds)) {
                    return true;
                }
            }
            return false;
        });
        if ($fields->count() === 0) {
            return response()->json([
                'success' => true,
                'message' => 'No fields found with selected types',
                'fields' => []
            ]);
        }

        $fields = $fields->map(function($field){
            $field->location;
            $field->currency;
            foreach ($field->types as $type) {
                $type->cost = number_format($type->pivot->cost, 2);
            }
            return $field;
        });

        return response()->json([
            'success' => true,
            'message' => 'Fields found',
            'fields' => $fields
        ]);
    }

    public function getField($id)
    {
        $field = Field::find($id);
        $field->cost = number_format($field->cost, 2);

        return response()->json([
            'success' => true,
            'field' => $field,
            'location' => Location::find($field->location_id),
            'currency' => Currency::find($field->currency_id),
        ]);
    }
}
