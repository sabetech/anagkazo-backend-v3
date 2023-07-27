<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Helpers\FedenaAPIHelper;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PastoralPoint;

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
                    ], 401);
            
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

            //TODO: save photo in the background in cloudinary and update the student record
        }

        $student->already_exists = $alreadyExists;
        return response()->json([
            'user' => $student,
            'success' => true
        ]);
    }

    public function authenticate(Request $request)
    {
        $indexNumber = $request->get('index_number');
        $pin = $request->get('passcode');

        $student = Student::where('index_number', $indexNumber)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Index Number. Please check and try again.'
                ], 401);
        }

        $user = User::where('email', $student->email_address)
            ->where('password', $pin)->first();
        
        if ($user){
            
            $user->api_token = Str::random(60);
            $user->save();

            return response()->json([
                'user' => $student,
                'token' => $user->api_token,
                'success' => true
            ], 200);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid Passcode. Please check and try again.'
            ], 401);
    }

    public function getBussing($indexNumber) {
        $student = Student::where('index_number', $indexNumber)->first();
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => 'Invalid Index Number. Please check and try again.'
            ], 400
        );
        return response()->json(
            [
                'success' => true,
                'data' => $student->bussing
            ], 200
        );
    }

    public function getPastoralPoints($indexNumber) {
        $student = Student::where('index_number', $indexNumber)->first();
        return $student->pastoralPoints;
    }

    public function postBussingDataFromClientForm(Request $request) {
        $date = date("Y-m-d", strtotime($request->get('bussing-date')));
        $student = Student::find($request->get('find-student'));
        if (!$student) return response()->json("Could not find student o!");

        $bussingDataRow = [];

        $bussingDataRow['st_attn'] = $request->get('present') === 'Yes' ? 1 : 0;
        $bussingDataRow['twn_attn'] = intVal($request->get('number_bussed'));
        $bussingDataRow['index_number'] = $student->index_number;

        Log::info("Bussing Date: " . $date);
        Log::info($bussingDataRow);

        $bussingSaved = Bussing::updateOrInsert($bussingDataRow, $date);

        if ($bussingSaved) {
            $student = Student::find($bussingSaved->student_id);

            return view('v2.admin_panel.prayer_track_response')
                ->with('bussing', $bussingDataRow['twn_attn'])
                ->with('pageTitle', 'Bussing Entry')
                ->with('date', date("M d, Y", strtotime($date)))
                ->with('student', $student);
        }
    }


}
