<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAttendanceCSV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-attendance-CSV2 {students_file} {event_file} {event}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        print_r("Reading Files ...\n");
        $students = $this->argument('students_file');
        $event = $this->argument('event_file');
        $eventCode = $this->argument('event');

        $studentfilePath = __DIR__ . "/".$students;
        $eventfilePath = __DIR__ . "/".$event;

        $studentsHandle = fopen($studentfilePath, 'r');
        $eventsHandle = fopen($eventfilePath,  'r');

        // Initialize an empty array to store CSV data
        $studentsArray = [];
        $eventsArray = [];

        // Read each line of the CSV file until the end
        $skipFirstRow = true;
        while (($row = fgetcsv($studentsHandle)) !== false) {
            // Add each row (as an array) to the data array
            if ($skipFirstRow) {
                $skipFirstRow = false;
                continue;
            }
            $studentsArray[] = $row;
        }

        $skipFirstRow = true;
        while (($row = fgetcsv($eventsHandle)) !== false) {
            // Add each row (as an array) to the data array
            if ($skipFirstRow) {
                $skipFirstRow = false;
                continue;
            }
            $eventsArray[] = $row;
        }
        // Close the file handle
        fclose($studentsHandle);
        fclose($eventsHandle);

        print_r("File Reading Complete!\n");

        print_r("Processing ...\n");

        $headings = ["Index Number", "Student Name", "batch"];
        $dates = self::generateDates($eventsArray);
        $headings = array_merge($headings, $dates);
        print_r("Dates Generated!\n");
        print_r("Indexing Attendance!\n");
        $indexArray = [];

        $progressbar = $this->output->createProgressBar(count($eventsArray));
        foreach($eventsArray as $index => $eventRow){

            $searchCriteria = ['student_id' => $eventRow[0], 'date' => $eventRow[1]];
            $result = array_filter($eventsArray, function($item) use ($searchCriteria) {
                return $item[0] === $searchCriteria['student_id'] && $item[1] === $searchCriteria['date'];
            });

            $result = array_values($result);

            if (count($result) == 1) {

                if ($result[0][3] == $eventCode . "_IN") {
                    $indexArray[$eventRow[0]][$eventRow[1]] = 'PRESENT';
                }else {
                    $indexArray[$eventRow[0]][$eventRow[1]] = 'PRESENT';
                }

            }

            if (count($result) > 1) {

                if ($result[0][3] == $eventCode . "_IN" && $result[ count($result) - 1 ][3] == $eventCode . "_OUT") {
                    $indexArray[$eventRow[0]][$eventRow[1]] = 'PRESENT';
                }
            }
            $progressbar->advance();
        }
        $progressbar->finish();

        $csvRow = [];
        $csvRow = $headings;

        $csvFile = __DIR__ . "/" . $eventCode . "_LECTURES_ALL.csv";
        $fileHandle = fopen($csvFile, 'w');
        fputcsv($fileHandle, $csvRow);

        print_r("\nWriting to file: $eventCode _LECTURES_ALL!\n");
        $progressbar1 = $this->output->createProgressBar(count($studentsArray));
        foreach($studentsArray as $studentRow) {

            $id = $studentRow[0];

            if (!isset( $indexArray[$id] )){
                continue;
            }

            $csvRow = [];

            $attnInfo = $indexArray[$id];

            $csvRow[] = $studentRow[1];
            $csvRow[] = $studentRow[2];
            $csvRow[] = $studentRow[3];

            foreach($dates as $date) {
                if  (!isset($attnInfo[$date])) {
                    $csvRow[] = 'ABSENT';
                } else {
                    $csvRow[] = $attnInfo[$date];
                }
            }

            fputcsv($fileHandle, $csvRow);
            $progressbar1->advance();

        }

        $progressbar1->finish();
        fclose($fileHandle);
        print_r("\nWriting to File Complete\n");
    }

    static function generateDates($attnInfo) {

        $dates = [];
        $dates = array_column($attnInfo, 1);

        $dates = array_unique($dates);

        return $dates;
    }
}
