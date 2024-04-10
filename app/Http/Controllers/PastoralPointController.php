<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parameter;
use App\Models\Student;
use App\Models\StudentPoint;
use Input;
use GuzzleHttp\Client;

class PastoralPointController extends Controller
{

    public function index()
    {
        return view('v2\admin_panel\new_pastoral');
    }

    public function search_student() {
        //show a form to search for student ...
        return view('client-view.pastoral_points.search-for-student');
    }

    public function pastoral_point_form(Request $request) {
        if (!$request->get('student_info')) return redirect("/pastoral_point_input");

        $studentInfo = $request->get('student_info');
        $indexNum = explode(" ", $studentInfo);
        $student = Student::where('index_number', $indexNum)->first();

        return view('client-view.pastoral_points.pastoral-point-form-new')
                ->with('student', $student);

    }

    public function getPastoralPointStudentProfile(Request $request) {

        $studentID = $request->get('student_id');
        $student = Student::find($studentID);

        if (!$student) return;
        $student->age = 0;

        if (($student->date_of_birth !== Null) || ($student->date_of_birth !== "0000-00-00"))
            $student->age = date_diff(date_create($student->date_of_birth), date_create('today'))->y;

        $studentBussingHistory = $student->bussing;
        $student->studentCenterService;
        $student->prayerLogs;
        $student->members;
        $student->sheepSeeking;
        $student->outreach;
        $student->counsellings;
        $student->center;

        //do roles here ...
        $roles = $student->studentRoles;
        $student->roles = [];
        foreach ($roles as $role) {
            $student->roles[] = $role->getRole();
        }

        $student->total = 0;
        foreach($student->pastoralPoints as $pastoralpoints) {
            $student->total += $pastoralpoints->pivot->points;
        }
        $parameters = Parameter::orderBy('id', 'asc')->get();

        $anagkazoLiveAttn = $student->attendances()
            ->where('event', 'ANAGKAZO LIVE')
            ->where('attendance_status', 'On Time')
            ->get();

        $student->totalDisciplinaryAction = 0;
        foreach($student->disciplinaryActions as $action) {
            $student->totalDisciplinaryAction += $action->pivot->points;
        }


       $image64 = self::getStudentImageFromFedena($student->index_number);

        return view('client-view.pastoral_points.student-view')
            ->with('student', $student)
            ->with('parameters', $parameters)
            ->with('bussing_history', $studentBussingHistory)
            ->with('image64', $image64)
            ->with('anagkazoLiveAttn', $anagkazoLiveAttn);

    }

    public static function getStudentImageFromFedena($indexNumber) {

        $fedena_url = "https://school.anagkazomanager.org/api/students/";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Token token="'.env("FEDENA_TOKEN").'"'
        ];

        $client = new Client();
        try {
            $res = $client->request(
                'GET',
                $fedena_url . $indexNumber,
                ['headers' => $headers]
            );

            $body = $res->getBody();
            $content = $body->getContents();
            $xml = simplexml_load_string($content);

            $jsonString = json_encode($xml);
            $studentDetailResponse = json_decode($jsonString, TRUE);

            return $studentDetailResponse["student"]["photo"]["image"];

        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

    }

    public function pastoral_form_submit(Request $request){
        $submittedData = $request->all();

        $student = Student::find($submittedData['student_id']);

        $totalPoints = 0;
        foreach($submittedData as $key => $paramPoint){

            if (intval($key) === 0) continue;

            $parameter = Parameter::find($key);
            $value = intval($paramPoint) * intval($parameter->weight);

             //update the students pastoral points values ..
             StudentPoint::where('student_id', $student->id)
                    ->where('parameter_id', $key)
                    ->update(['points' => $value]);

             $totalPoints += $value;
        }

        return view('client-view.pastoral_points.response')
            ->with('student', $student)
            ->with('total_point', $totalPoints);
    }

    public function getPastoralPointLive(Request $request){
        $class_id = $request->get('class_id', 20);
        $parameters = Parameter::orderBy('id', 'asc')->get();
        $students = Student::where('class_id', $class_id)->with('pastoralPoints')->orderBy('id', 'asc')->get();

        foreach($students as $student) {
            $student->total = 0;
            foreach($student->pastoralPoints as $pastoralpoints) {
                $student->total += $pastoralpoints->pivot->points;
            }
        }

        $students = $students->sortBy(function ($student) {
            return $student->total;
        });

        return view('client-view.pastoral_points.results')
            ->with('parameters', $parameters)
            ->with('class_selection', $class_id)
            ->with('students', $students);

    }

    public function getPastoralParams(){
        $studentID = Input::get('id');

        $params = Parameter::join('student_point', 'student_point.parameter_id','=','parameters.id')
                           ->join('students','students.id','=','student_point.student_id')
                           ->where('students.id', $studentID)
                           ->get()
                           ->toArray();

        return json_encode($params);

    }
}
