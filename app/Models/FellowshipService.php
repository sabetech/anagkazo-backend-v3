<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FellowshipService extends Model
{
    use HasFactory;

    protected $table = 'fellowship_service';
    protected $guarded = ['id'];

    public static function getFellowshipServices(string $date) {
        $services = self::where('service_date', $date)->get();

        return $services;
    }

    public static function getFellowshipServiceDefaulters($date) {
        //get students who didn't fill the form on this date.
        //first check if there was service on that date

    }
}
