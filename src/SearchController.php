<?php

namespace Xlr8rms\Hotelsearchapi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    /**
     * getNearbyHotels method fetch and process hotel list according to requested parameters
     * @param Request $request
     * @return mixed
     */
    public function getNearbyHotels(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ],
        [
            'latitude.required' => 'latitude is required',
            'longitude.required' => 'longitude is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => false, 'error_msg' => $validator->errors()], 404);
        }
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $orderBy = isset($request->orderby) ? $request->orderby : 'proximity';
        $hotelsList = getHotelsList($latitude, $longitude, $orderBy);
        // set output response 
        if (is_object($hotelsList)) {
            return response()->json(['success' => true, 'hotels_list' => $hotelsList], 200);
        } else {
            return response()->json(['success' => false, 'error_msg' => $hotelsList], 404);
        }
    }
}
