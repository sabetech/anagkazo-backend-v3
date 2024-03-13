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

    public static function saveFedenaStudent($fedenaStudent, $admission_no)
    {
        $theFedenaStudent = $fedenaStudent['student'];

        $classInfo = StudentClass::where('class', $theFedenaStudent['batch_name'])->first();
        if (isset($theFedenaStudent['batch_name'])) {
            if (!$classInfo)
                $classInfo = StudentClass::create([
                    'class' => $theFedenaStudent['batch_name'],
                    'class_size' => 0
                ]);
        }

        $student = Student::withTrashed()->where('index_number', $admission_no)->first();
        if (!$student) {
            $student = new Student;
        }

        if ($student->trashed()) {
            $student->restore();
        }

        $student->name = isset($theFedenaStudent['student_name']) ? $theFedenaStudent['student_name'] : "Empty Name";
        $student->index_number = $admission_no;
        $student->gender = isset($theFedenaStudent['gender']) ? $theFedenaStudent['gender'] : 'x';
        $student->class_id = $classInfo->id;
        $student->class = $classInfo->class;
        $student->country = $theFedenaStudent['country'];
        $student->phone = $theFedenaStudent['mobile'];
        $student->email_address = $theFedenaStudent['email'];
        $student->photo_url = self::getImageFromBase64($theFedenaStudent['photo']['image'], $admission_no);
        $student->city = $theFedenaStudent['city'];

        $student->save();

        return $student;
    }

    public static function getImageFromBase64($imageBase64, $admission_no)
    {
        $extension = explode('/', explode(':', substr($imageBase64, 0, strpos($imageBase64, ';')))[1])[1];
        $replace = substr($imageBase64, 0, strpos($imageBase64, ',') + 1);

        $image = str_replace($replace, '', $imageBase64);
        $image = str_replace(' ', '+', $image);
        $imageName = $admission_no . '.' . $extension;

        Storage::disk('public')->put('student_photo/' . $imageName, base64_decode($image));

        return "storage/student_photo/" . $imageName;
    }

}
