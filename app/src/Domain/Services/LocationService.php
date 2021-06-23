<?php


namespace App\src\Domain\Services;


interface LocationService
{
    public function create($lat, $lng, $country, $country_code, $province, $province_code, $city, $place_id, $formatted_address, $is_by_lat_lng);

    public function update($locationId, $lat, $lng, $country, $country_code, $province, $province_code, $city, $place_id, $formatted_address, $is_by_lat_lng);
}
