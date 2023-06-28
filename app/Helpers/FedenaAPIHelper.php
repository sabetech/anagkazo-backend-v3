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
        
        Log::info($response->body());
        $xml = simplexml_load_string($body);
        $jsonString = json_encode($xml);
        $studentInfo = json_decode($jsonString, FALSE);

        Log::info($studentInfo);

        /*
        $request = new Request('GET', env('FEDENA_BASE_URL') . $indexNumber, $headers);

        $promise = $client->sendAsync($request)->then(function (Response $response) {
            return $response->getBody();
        }, function ($e) {
            return $e;
        });

        $promise->then(
            function ($body) {
            $xml = simplexml_load_string($body);
            $jsonString = json_encode($xml);
            $studentInfoArray = json_decode($jsonString, TRUE);
            
            $newStudent = Student::saveFedenaStudent($studentInfoArray, $studentInfoArray['admission_no']);
            return $newStudent;
        }, 
        function ($reason) {
            Log::Error($reason->getMessage());
            return false;
        });

        $promise->wait();
        */
    }


}