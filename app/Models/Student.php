<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PastoralPoint;
use App\Models\Bussing;
use App\Helpers\FedenaBackend;

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

    public function bussing()
    {
        return $this->hasMany(Bussing::class, 'student_id', 'id');
    }

    public static function saveStudentsFromFedena($studentsArrayFromFedena)
    {
        ini_set('max_execution_time', 300);
        $admissionNumbers = []; /// make a request for these...
        foreach ($studentsArrayFromFedena as $fedenaStudent) {
            if (Student::withTrashed()->where('index_number', trim($fedenaStudent['admission_no']))->exists()) continue;

            $admissionNumbers[] = $fedenaStudent;
        }
        FedenaBackend::getFedenaStudentDetail($admissionNumbers, 'student'); //$fedenaStudent['admission_no']
    }

}
