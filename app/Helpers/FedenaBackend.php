<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class FedenaBackend
{

    public static function fetchStudentsFromFedena()
    {

        $fedena_url = "https://school.anagkazomanager.org/api/";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="' . env("FEDENA_TOKEN") . '"'
        ];

        $client = new Client(['base_uri' => $fedena_url]);
        try {
            $res = $client->request(
                'GET',
                'students',
                ['query' => ['search[]' => ""], 'headers' => $headers]
            );

            $body = $res->getBody();
            $content = $body->getContents();
            $xml = simplexml_load_string($content);

            $jsonString = json_encode($xml);
            $studentArray = json_decode($jsonString, TRUE);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }


        return $studentArray;
    }

    public static function getFedenaStudentDetail($admissionNumbers, $source)
    {

        $fedena_url = "https://school.anagkazomanager.org/api/students/";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="' . env("FEDENA_TOKEN") . '"'
        ];

        $client = new Client();
        $requests = function ($total) use ($fedena_url, $headers, $admissionNumbers) {
            $uri =  $fedena_url;
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $uri . $admissionNumbers[$i]['admission_no'], $headers);
            }
        };

        $pool = new Pool($client, $requests(count($admissionNumbers)), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) use ($admissionNumbers, $source) {
                // this is delivered each successful response
                $body = $response->getBody();
                $content = $body->getContents();
                $xml = simplexml_load_string($content);

                $jsonString = json_encode($xml);
                $studentInfoArray = json_decode($jsonString, TRUE);
                if ($source == 'student')
                    Student::saveFedenaStudent($studentInfoArray, $admissionNumbers[$index]['admission_no']);
                else {
                    AnagkazoStudent::saveFedenaStudent($studentInfoArray, $admissionNumbers[$index]['admission_no']);
                }
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
                Log::info($reason->getMessage());
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        Log::info("DONE GETTING ALL THE STUDENTS");
    }

    public static function getFedenaClasses()
    {

        $fedena_url = "https://school.anagkazomanager.org/api/";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="' . env("FEDENA_TOKEN") . '"'
        ];

        $client = new Client(['base_uri' => $fedena_url]);
        $res = $client->request(
            'GET',
            'batches',
            ['query' => ['search[]' => ""], 'headers' => $headers]
        );

        $body = $res->getBody();
        $content = $body->getContents();
        $xml = simplexml_load_string($content);

        $jsonString = json_encode($xml);
        $studentInfoArray = json_decode($jsonString, TRUE);

        return $studentInfoArray;
    }
}
