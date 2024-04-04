<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Parameter;
use App\Models\Student;

class GeneratePastoralPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:pastoral_points {class_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command generates pastoral points for the class id specified.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $class_id = $this->argument('class_id');
        $handle = fopen("pastoral_point_{$class_id}.csv", 'w');
        $headings = array_merge(['index_number', 'name', 'class'], Parameter::pluck('parameter')->toArray());



        fputcsv($handle, $headings);
        $students = Student::where('class_id', '=', $class_id)->get();
        foreach ($students as $student) {
            $row = [];
            $pastoralPoints = $student->pastoralPoints;

            $row[] = $student->index_number;
            $row[] = $student->name;
            $row[] = $student->class;

            foreach ($pastoralPoints as $pastoralPoint) {
                $row[] = $pastoralPoint->pivot->points;
            }
            fputcsv($handle, $row);
        }

        fclose($handle);
    }
}
