<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegacyAnagkazoAttendance;

class GenerateAttendanceCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-attendance-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generates a csv file of the attendance data using the old database. New attendance data can be exported from the web interface!';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get class to be worked on as an arg from cmd line.
        $class = $this->argument('class');
        $event = $this->argument('event');

        $fromBeginning = true;

        $dateFrom = LegacyAnagkazoAttendance::getStartingDate($event, $classId);
        $dateTo = LegacyAnagkazoAttendance::getEndDate($event, $classId);




    }

    public function generateAttendanceReport($classId, $event, $dateFrom, $dateTo) {

        $headings = $this->generateHeadings($dateFrom, $dateTo, $event);

        $students = $this->getStudents($classId);

        $outQrText = self::determinOutText($event);

        $attendanceRecords = LegacyAnagkazoAttendance::where('event', 'LIKE', '%' . $event . '%')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->whereIn('student_id', $students->pluck('id')->toArray())
            ->get();

        $attendanceOutRecords = LegacyAnagkazoAttendance::where('event', 'LIKE', '%' . $outQrText . '%')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->whereIn('student_id', $students->pluck('id')->toArray())
            ->get();


    }

    public static function determinOutText($inText)
    {
        switch ($inText) {

            case "VISION_IN":
                return "VISION_OUT";

            case "PILLAR_IN":
                return "PILLAR_OUT";

            case "BMCDR_IN":
                return "BMCDR_OUT";

            case "WOG_IN":
                return "WOG_OUT";

            case "ANAGKAZO_LIVE_IN":
                return "ANAGKAZO_LIVE_OUT";

            case "FOUNDATIONAL_IN":
                return "FOUNDATIONAL_OUT";

            case "TUTORIAL_IN":
                return "TUTORIAL_IN";

            case "4AM_PRAYERS_IN":
                return "4AM_PRAYERS_OUT";

            case "SUNDAY_SERVICE_IN":
                return "SUNDAY_SERVICE_OUT";

            case "ANAGKAZO_ENCOUNTER_SERVICE_IN":
                return "ANAGKAZO_ENCOUNTER_SERVICE_OUT";

            default:
                return "";
        }
    }

    public function generateHeadings() {
        $headingDates = LegacyAnagkazoAttendance::getDateHeadingsFromRange($dateFrom, $dateTo, $event);
        $headings = array_merge(['index number', 'name', 'batch'], $headingDates);

        return $headings;
    }

    public function getStudents($class) {
        $students = DB::table('anagkazo_students')->where('batch', $class)->get();
        return $students;
    }
}
