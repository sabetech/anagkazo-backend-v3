<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyAnagkazoAttendance extends Model
{
    use HasFactory;

    protected $connection= 'mysql2';
    protected $table = 'anagkazo_attendance';
    protected $guarded = ['id'];

    public static function getStartingDate($event, $class)
    {
        return self::getDate($event, $class, 'asc');
    }

    public static function getEndDate($event, $class)
    {
        return self::getDate($event, $class, 'desc');
    }

    public static function getDate($event, $class, $order = 'asc')
    {
        if ($class === 'all') {
            return self::where('event', 'LIKE', '%' . $event . '%')->orderBy('date', $order)->pluck('date')->first();
        }

        return DB::table('anagkazo_students')->where('batch', $class)
            ->join('anagkazo_attendance', 'anagkazo_attendance.student_id', '=', 'students.id')
            ->where('event', 'LIKE', '%' . $event . '%')
            ->orderBy('anagkazo_attendance.date', $order)
            ->pluck('date')
            ->first();
    }

    public static function getDateHeadingsFromRange($from, $to, $event) {
        return self::whereBetween('date', [$from, $to])
            ->where('event', 'LIKE', '%' . $event . '%')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date')
            ->toArray();
    }

}
