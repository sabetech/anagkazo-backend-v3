<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Parameter;
use App\Models\StudentPoint;
use DB;

class StudentController extends Controller
{
    //
    public function pastoral_point_input()
    {
        return view('v2.admin_panel.pastoral_points_input');
    }

    public function pastoral_point_input_post(Request $request)
    {
        $posted = Input::get();

        $studentID = $posted['student_id'];
        $pastoralPoints = $posted['pastoral_points'];

        $student = Student::find($studentID);
        if (!$student) return; // put some nice error here ...

        $totalPoints = 0;
        foreach ($pastoralPoints as $pastoralPointID) {

            $weight = Parameter::where('id', $pastoralPointID)->first();
            $value = $weight->weight;
            if ($weight->data_type === 'integer') {
                $value = $value * intval($posted[$pastoralPointID]);
            }

            //update the students pastoral points values ..
            StudentPoint::where('student_id', $student->id)
                ->where('parameter_id', $pastoralPointID)
                ->update(['points' => $value]);

            $totalPoints += $value;
        }

        //remember to add bussing points to it ...

        return view('v2.admin_panel.pastoral_points_reponse')
            ->with('student', $student)
            ->with('points', $totalPoints);
    }

    public function searchStudentsOnly(Request $request)
    {
        $searchTerm = $request->get('search');

        $result = Student::where('students.name', 'LIKE', '%' . $searchTerm . '%')
            ->select(DB::raw('students.id as id, CONCAT(students.index_number, " ",students.name)  as text'))
            ->get()
            ->take(10);

        return response()->json($result);
    }
}
