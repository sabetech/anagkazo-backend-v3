<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PastoralPoint;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'students';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at'];

    public function pastoralPoints()
    {
        return $this->belongsToMany(PastoralPoint::class, 'student_point', 'student_id', 'parameter_id')->withPivot('points');
    }

}
