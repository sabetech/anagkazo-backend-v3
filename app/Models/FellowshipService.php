<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FellowshipService extends Model
{
    use HasFactory;

    protected $table = 'fellowship_service';
    protected $guarded = ['id'];

    public function getFellowshipServices(string $from, string $to) {
        $services = self::whereBetween('service_date', [$from, $to])->get();

        return $services;
    }

    public function getFellowshipServiceDefaulters($date) {
        //get students who didn't fill the form on this date.
        //first check if there was service on that date

    }
}
