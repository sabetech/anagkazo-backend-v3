<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FellowshipService;
use App\Models\Student;
use App\Models\StudentClass;

class generate_fellowship_report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate_fellowship_report {class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generates fellowship report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $class = $this->argument('class');
        $class_id = StudentClass::where('class', $class)->pluck('id')->first();

        $student_ids = Student::where('class_id', $class_id)->pluck('id')->toArray();

        $headers = ['index_number', 'name', 'serive_date', 'attendance', 'offering', 'foreign_offering'];
        $fellowshipServices = FellowshipService::join('students', 'students.id', '=', 'fellowship_service.student_id')
                                ->whereIn('student_id', $student_ids)
                                ->select($headers)->get();

        //create a csv file
        $fp = fopen('fellowship_report.csv', 'w');

        fputcsv($fp, $headers);
        foreach($fellowshipServices as $fellowshipService) {

            fputcsv($fp, $fellowshipService->toArray());

        }

        fclose($fp);
    }
}
