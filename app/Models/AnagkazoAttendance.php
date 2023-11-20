<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnagkazoAttendance extends Model
{
    use HasFactory;
    
    protected $table = 'anagkazo_attendance';
    protected $connection = 'mysql2';
}