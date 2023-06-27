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
                return response()->json(['response' => 'Index Number is Invalid'], 400);
        }

        return response()->json([
            'user' => $student,
            'already_exists' => $alreadyExists,
            'success' => true
        ]);
    }
}
