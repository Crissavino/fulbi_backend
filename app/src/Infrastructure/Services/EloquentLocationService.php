<?php


namespace App\src\Infrastructure\Services;


use App\Models\Location;
use App\src\Domain\Services\LocationService;

class EloquentLocationService implements LocationService
{

    public function create($lat, $lng, $country, $country_code, $province, $province_code, $city, $place_id, $formatted_address, $is_by_lat_lng)
    {
        return Location::create([
            'lat' => $lat,
            'lng' => $lng,
            'country'=> $country,
            'country_code'=> $country_code,
            'province'=> $province,
            'province_code'=> $province_code,
            'city'=> $city,
            'place_id' => $place_id,
            'formatted_address'=> $formatted_address,
            'is_by_lat_lng'=> $is_by_lat_lng,
        ]);
    }

    public function update($locationId, $lat, $lng, $country, $country_code, $province, $province_code, $city, $place_id, $formatted_address, $is_by_lat_lng)
    {
        return Location::find($locationId)->update([
            'lat' => $lat,
            'lng' => $lng,
            'country'=> $country,
            'country_code'=> $country_code,
            'province'=> $province,
            'province_code'=> $province_code,
            'city'=> $city,
            'place_id' => $place_id,
            'formatted_address'=> $formatted_address,
            'is_by_lat_lng'=> $is_by_lat_lng,
        ]);
    }
}
