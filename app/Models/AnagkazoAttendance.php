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
        
        Log::info(json_encode($scanInfo));

        $event = $scanInfo['event'];
        $inOrOut = $scanInfo['mode'];
        $lateCondition = $scanInfo['late_condition'];
        $date = $scanInfo['date'];
        
        if ($scanInfo['date'] === 'null'){
            $date = date("Y-m-d");
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

    // public static function inOrOut($scannedEvent){
    //     if (!strrpos($scannedEvent, '_IN')) {
    //         return 'OUT';
    //     } else {
    //         return 'IN';
    //     }
    // }

    // public static function getEvent($scannedEvent) {
    //     $event = false;
    //     if (strrpos($scannedEvent, '_IN')) {
    //         return substr($scannedEvent, strrpos($scannedEvent, '_IN'));
    //     }
    //     substr($scannedEvent, strrpos($scannedEvent, '_OUT'));
    // }    
}
