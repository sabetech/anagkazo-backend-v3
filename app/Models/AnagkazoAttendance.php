<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnagkazoAttendance extends Model
{
    use HasFactory;
    
    protected $table = 'attendance_2';
    protected $guarded = ['id'];
    
    public static function handleScanInformation($student, $scanInfo) {
        
        $event = self::getEvent($scanInfo['event']);
        $inOrOut = self::inOrOut($scanInfo['event']);
        $lateCondition = $scanInfo['late_condition'];

        switch($inOrOut) {
            case "IN":
                self::saveScanIN($student, $event, $lateCondition);
                break;
            case "OUT":
                self::saveScanOUT($student, $event);
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

    public static function inOrOut($scannedEvent){
        if (!strrpos($scannedEvent, '_IN')) {
            return 'OUT';
        } else {
            return 'IN';
        }
    }

    public static function getEvent($scannedEvent) {
        $event = false;
        if (strrpos($scannedEvent, '_IN')) {
            return substr($scannedEvent, strrpos($scannedEvent, '_IN'));
        }
        substr($scannedEvent, strrpos($scannedEvent, '_OUT'));
    }


    
}
