<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FellowshipService;

class FellowshipServiceAPIController extends Controller
{
    //
    public function fellowshipServices(Request $request) {
        $from = $request->get('from');
        $to = $request->get('to');

        $fellowshipServices = FellowshipService::getFellowshipServices($from, $to);

        return response()->json([
            'success' => true,
            'data' => $fellowshipServices
        ], 200);
    }
}
