<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'students';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at'];

    

}
