<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FellowshipService;

class FellowshipServiceAPIController extends Controller
{
    //
    public function fellowshipServices(Request $request) {
        $date = $request->get('date');

        $fellowshipServices = FellowshipService::getFellowshipServices($date);

        return response()->json([
            'success' => true,
            'data' => $fellowshipServices
        ], 200);
    }
}
