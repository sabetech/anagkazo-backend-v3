<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AnagkazoAttendance extends Model
{
    use HasFactory;

    protected $table = 'attendance_2';
    protected $guarded = ['id'];

    public static function handleScanInformation($student, $scanInfo) {

        Log::info($scanInfo);

        $event = $scanInfo['event'];
        $inOrOut = $scanInfo['mode'];
        $lateCondition = $scanInfo['late_condition'];
        $date = $scanInfo['date'];

        if (strtolower($scanInfo['date']) === 'null'){
            $date = date("Y-m-d");
        }

        if (strtolower($scanInfo['late_condition']) === 'null'){
            $lateCondition = null;
        }

        Log::info("EVENT::" . $event);
        Log::info("IN OR OUT::". $inOrOut);
        Log::info("LATE CONDITION::". $lateCondition);
        Log::info("DATE::".$date);

        switch($inOrOut) {
            case "IN":
                self::saveScanIN($student, $event, $date, $lateCondition);
                break;
            case "OUT":
                self::saveScanOUT($student, $event, $date);
                break;
        }

    }

    public static function saveScanIN($student, $event, $date, $lateCondition) {
        $saved = self::updateOrCreate([
            'student_id' => $student->id,
            'date' => $date,
            'event' => $event,
        ],
        [
            'time_in' => date('H:i:s'),
            'late_condition' => $lateCondition,
        ]);

        if ($saved)
            return true;
         return false;
    }

    public static function saveScanOut($student, $event, $date) {
        $saved = self::updateOrCreate([
            'student_id' => $student->id,
            'date' => $date,
            'event' => $event,
        ],
        [
            'time_out' => date('H:i:s'),
        ]);

        if ($saved)
            return true;
        return false;
    }

    public static function getStartingDate($event, $classId)
    {
        return AnagkazoAttendance::getDate($event, $classId, 'asc');
    }

    public static function getEndDate($event, $classId)
    {
        return AnagkazoAttendance::getDate($event, $classId, 'desc');
    }

    public static function getDate($event, $classId, $order = 'asc')
    {
        if ($classId === 'all') {
            return AnagkazoAttendance::where('event', 'LIKE', '%' . $event . '%')->orderBy('date', $order)->pluck('date')->first();
        }

        return Student::where('class', $classId)
            ->join('attendance_2', 'attendance_2.student_id', '=', 'students.id')
            ->where('event', 'LIKE', '%' . $event . '%')
            ->orderBy('attendance_2.date', $order)
            ->pluck('date')
            ->first();
    }

    public static function getDateHeadingsFromRange($from, $to, $event)
    {
        return AnagkazoAttendance::whereBetween('date', [$from, $to])
            ->where('event', 'LIKE', '%' . $event . '%')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date')
            ->toArray();
    }

    public static function getAttendanceExcelStructure($dateFrom, $dateTo, $event, $classId, $dates) {
        $students = self::getStudents($classId);

        $attendanceRecords = AnagkazoAttendance::where('event', 'LIKE', '%' . $event . '%')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->whereIn('student_id', $students->pluck('id')->toArray())
            ->get();

        $buildExportArray = [];
        foreach ($students as $student) {
            $row = [];
            $row[] = $student->admission_no;
            $row[] = $student->name;
            $row[] = $student->batch;

            foreach ($dates as $date) {
                $attnRecord = $attendanceRecords->search(function ($rec, $key) {
                    return $rec->student_id == $student->id;
                });

                if ($attnRecord) {
                    if (($attnRecord->time_in) && ($attnRecord->time_out)) {
                        $row[] = "PRESENT [ " . $attnRecord->time_in . " ]";
                    }else {
                        $row[] = "ABSENT";
                    }
                }else{
                    $row[] = "ABSENT";
                }
            }
            $buildExportArray[] = $row;
        }
        return $buildExportArray;
    }

    public static function synchronizeAttendance($attendanceData) {
        Log::info($attendanceData);
        /*
            [
                date =>
                event =>
                time_in =>
                time_out =>
            ]
        */
    }

}
