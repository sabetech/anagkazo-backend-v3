<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Parameter;
use App\Models\Student;

class GeneratePastoralPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-pastoral-point {class_id}';

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
