<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\RequestException;
use App\Models\Student;

class UpdateStudentsFromFedena extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-students-from-fedena';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the students list from fedena API to local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $fedenaStudents = self::fetchStudentsFromFedena();
        echo "PROGRESS: 25% -> Students Feteched from Fedena\n";

        self::verifyStudentsFromFedena($fedenaStudents);

        self::saveStudentsFromFedena($fedenaStudents['student']);

        echo "PROGRESS: 75% -> Students Saved Into Academic DB\n";
        $this->removeNonExistentStudents($fedenaStudents['student']);

        echo "==========================================\n";
        echo "100% -> DONE FETCHING STUDENTS FROM FEDENA\n";

    }

    public static function fetchStudentsFromFedena()
    {

        $fedena_url = "https://school.anagkazomanager.org/api/";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="' . env("FEDENA_API_TOKEN") . '"'
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
            echo $e->getMessage() . "\n";
            return  [];
        }

        return $studentArray;
    }

    public static function saveStudentsFromFedena($studentsArrayFromFedena)
    {
        ini_set('max_execution_time', 300);
        $admissionNumbers = []; /// make a request for these...
        echo ":::Students to be saved:::\n";
        foreach ($studentsArrayFromFedena as $fedenaStudent) {
            // if (Student::where('index_number', trim($fedenaStudent['admission_no']))->exists()) continue;

            $admissionNumbers[] = $fedenaStudent;
            echo "About to Save:::".$fedenaStudent['admission_no'] . ":- ".$fedenaStudent['student_name']."\n";

        }
        self::getFedenaStudentDetail($admissionNumbers, 'student'); //$fedenaStudent['admission_no']
    }

    public static function getFedenaStudentDetail($admissionNumbers, $source)
    {

        $fedena_url = "https://school.anagkazomanager.org/api/students/";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="' . env("FEDENA_API_TOKEN") . '"'
        ];

        $client = new Client();
        $requests = function ($total) use ($fedena_url, $headers, $admissionNumbers) {
            $uri =  $fedena_url;
            echo "TOTAL::: " . $total."\n";
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

                Student::saveFedenaStudent($studentInfoArray, $admissionNumbers[$index]['admission_no']);

                echo "Student ".$admissionNumbers[$index]['admission_no']." saved!\n";
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
                Log::info($reason->getMessage());
                echo "ERROR::: Student not saved::".$reason->getMessage();
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        Log::info("DONE GETTING ALL THE STUDENTS");
    }

    public function removeNonExistentStudents($fedenaStudents)
    {
        $students = Student::pluck('index_number')->toArray();

        foreach ($fedenaStudents as $fedenaStudent) {
            if (($key = array_search($fedenaStudent['admission_no'], $students)) !== false) {
                unset($students[$key]);
            }
        }

        Student::whereIn('index_number', $students)->delete();
    }

    public static function verifyStudentsFromFedena($fedenaStudents) {
        echo "Number of Fedena Students::".count($fedenaStudents["student"]) . "\n";

        echo ":::POTENTIAL STUDENTS TO BE SAVED:::\n\n";
        $count = 0;
        foreach ($fedenaStudents["student"] as $potentialStudent) {

            echo ++$count."Student ".$potentialStudent['student_name'] ."(".$potentialStudent['admission_no'].") ". $potentialStudent['batch_name'] . "\n";

        }
        echo "DONE VERIFYING STUDENTS";
    }

}
