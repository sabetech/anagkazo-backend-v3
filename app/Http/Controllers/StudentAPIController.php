<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Helpers\FedenaAPIHelper;

class StudentAPIController extends Controller
{
    //
    public function verifyIndexNumber(Request $request)
    {
        $indexNumber = $request->get('index_number');
        $alreadyExists = true;
        $student = Student::where('index_number', $indexNumber)->first();
        if (!$student) {
            //check if student is in fedena
            $student = FedenaAPIHelper::fetchStudent($indexNumber);
            $alreadyExists = false;

            if (!$student)
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Index Number. Please check and try again.'
                    ], 400);
            
            //save student
            $student = Student::create([
                "index_number" => $student->index_number,
                "name" => $student->student_name,
                "email_address" => $student->email,
                "phone" => $student->mobile,
                "class" => $student->batch_name,
                "gender" => $student->gender,
                "date_of_birth" => $student->date_of_birth,
                "country" => $student->country,
            ]);

            //TODO: save photo in the background and update the student record
        }

        $student->already_exists = $alreadyExists;
        return response()->json([
            'user' => $student,
            'success' => true
        ]);
    }
}
