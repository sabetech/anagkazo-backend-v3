<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Log;

class FedenaAPIHelper {

    public static function fetchStudent(int $indexNumber) {
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="'. env('FEDENA_API_TOKEN') .'"'
        ];

        $response = Http::withHeaders($headers)->timeout(15)->get(env('FEDENA_BASE_URL') . $indexNumber);
        
        $xml = simplexml_load_string($response->body());
        $jsonString = json_encode($xml);
        $studentInfo = json_decode($jsonString, FALSE);

        Log::info(print_r($studentInfo, TRUE));

        if (isset($studentInfo->student)) {
            $student = $studentInfo->student;
            $student->index_number = $indexNumber;
            return $student;
        }else{
            return false;
        }

    }


}