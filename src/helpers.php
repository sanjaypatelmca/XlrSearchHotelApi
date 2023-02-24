<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
* this is a helper function in which CALL JSON API for Get Hotel List
*/

if (!function_exists('getHotelsList')) {
    /**
     * @param $latitude
     * @param $longitude
     * @param $orderBy
     * @return string
     */
    function getHotelsList($latitude, $longitude, $orderBy)
    {
        $sourceEndPoints = getHotelsEndPointSources();

        if ($sourceEndPoints && count($sourceEndPoints)) {
                $result = [];
                foreach ($sourceEndPoints as $endPoint) {
                    // below code CALL JSON API for Get Hotels List from json sources from $endPoint;
                    $client = new Client();
                    $response =   $client->request('GET', $endPoint);
                    $content = json_decode($response->getBody(), true);

                    // Get Response success then merge data with KM operation 
                    if ($content && isset($content['success']) && $content['success'] == true) {
                        foreach ($content['message'] as $key => $value) {
                            // validate the data which are get from JSON API. some key value miss-match then this record skip in array
                            $hotelLatitude = isset($value[1]) ? trim($value[1]) : null;
                            $hotelLongitude = isset($value[2]) ? trim($value[2]) : null;
                            try{
                                if (validateLatitude($hotelLatitude) && validateLongitude($hotelLongitude)) {
                                    // calculate KM based on user API requested latitude and longitude and hotel item's latitude and longitude
                                    $calculateDistance = getPointToPointDistance(
                                                                                $hotelLatitude,
                                                                                $hotelLongitude,
                                                                                $latitude,
                                                                                $longitude
                                                                            );

                                    if (is_numeric($calculateDistance)) {
                                        $result[$key]['hotel'] = $value[0];
                                        $result[$key]['distance'] = $calculateDistance . ' KM';
                                        $result[$key]['price'] = $value[3] . ' EUR';
                                    }
                                } else {
                                    // invalid hotelLatitude and hotelLongitude data 
                                    Log::error('invalid hotel data', $value);
                                }
                           }catch(\Exception $e){
                                Log::error('Error', [$e->getMessage()]);
                           }
                            
                        }
                    }
                }

                // Create new finalize array for sort
                $sortedData = collect($result);

                // sort data based on param 
                if ($orderBy == 'pricepernight') {
                    $sortedRecords = $sortedData->sortBy('price');
                } else {
                    $sortedRecords = $sortedData->sortBy('distance');
                }

                //Arrange data to match desired output
                $sortedHotelsList = $sortedRecords->values()->map(function ($item) {
                    return $item;
                });

                //return sorted data
                return $sortedHotelsList; 
        }        
    }
}

if (!function_exists('getHotelsEndPointSources')) {
    /**
     * @return string[]
     */
    function getHotelsEndPointSources()
    {
        // Note : Here below sources are static, But we can manage to call external api or other dynamic db source to fetch dynamic list of sources endpoints and arrange and return in array format as below
        return [
            'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_1.json',
            'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json'
        ];
    }
}

if (!function_exists('getPointToPointDistance')) {
    /**
     * getPointToPointDistance function calculate KM based on Lat and Long values
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float|string
     */
    function getPointToPointDistance($lat1, $lon1, $lat2, $lon2)
    {
        try {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            return round($miles * 1.609344, 2);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

if (!function_exists('validateLatitude')) {
    /**
     * @param $latitude
     * @return bool
     */
    function validateLatitude($latitude)
    {
        if (is_numeric($latitude) && $latitude >= -90 && $latitude <= 90) {
            return true;
        }
        return false;
    }
}

if (!function_exists('validateLongitude')) {
    /**
     * @param $longitude
     * @return bool
     */
    function validateLongitude($longitude)
    {
        if (is_numeric($longitude) && $longitude >= -180 && $longitude <= 180) {
            return true;
        }
        return false;
    }
}
