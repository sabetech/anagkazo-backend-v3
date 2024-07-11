<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentClass;
use App\Helpers\FedenaAPIHelper;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PastoralPoint;
use App\Models\Bussing;
use App\Models\AnagkazoAttendance;
use App\Models\FellowshipService;
use App\Exports\AttendanceExport;
use App\Exports\GenerateAttendanceReport;
use App\Exports\GenerateCSVAttendanceReport;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;


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

            //create a passcode on the fly and give it to the student as part of the response.
            $passcode = rand(1000, 9999);

            $user = User::create([
                'name' => $student->name,
                'user_type' => 0,
                'email' => $student->email_address,
                'password' => $passcode
            ]);

            $student->passcode = $passcode;

            return response()->json([
                'user' => $student,
                'token' => $user->api_token,
                'success' => true
            ], 200);
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
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => 'Invalid Index Number. Please check and try again.'
            ], 400
        );

        return response()->json(
            [
                'success' => true,
                'data' => $student->pastoralPoints
            ], 200
        );
    }

    public function postBussing(Request $request, $indexNumber) {
        $student = Student::where('index_number', $indexNumber)->first();
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => 'Invalid Index Number. Please check and try again.'
            ], 400
        );

        $bussingDataRow = [];

        $uploadedFileUrl = null;
        //get uploaded image from request
        if ($request->file('bussing_image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('bussing_image')->getRealPath(), [
                'folder' => 'Anagkazo.Apps'
            ])->getSecurePath();
        }else{
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Could not save bussing Image. Please try again.'
                ], 400
            );
        }

        Log::info($uploadedFileUrl);

        $bussingDataRow['st_attn'] = 1;
        $bussingDataRow['twn_attn'] = intVal($request->get('number_bussed'));
        $bussingDataRow['index_number'] = $student->index_number;
        $bussingDataRow['cloudinary_img_id'] = $uploadedFileUrl;

        Log::info($bussingDataRow);

        $bussingSaved = Bussing::updateOrInsert($bussingDataRow, $request->get('date'));

        if ($bussingSaved) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $bussingSaved
                ], 200
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'Could not save bussing data. Please try again.'
            ], 400
        );
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

    public function postAttendance($id, Request $request) {
        $student = Student::find($id);
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => 'Student Not Found! ID is not valid. Please check and try again.'
            ], 404
        );

        Log::info("SCANNINFO::",$request->all());
        $result = AnagkazoAttendance::handleScanInformation($student, $request->all());

        return response()->json(
            [
                'success' => true,
                'message' => 'Attendance Saved successfully'
            ], 200
        );
    }

    public function getAttendance($indexNumber, Request $request) {
        $event = $request->get('event');

        $student = Student::where('index_number', $indexNumber)->first();
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => 'Student Not Found! ID is not valid. Please check and try again.'
            ], 404
        );

        $attendance = AnagkazoAttendance::where('student_id', $student->id)
            ->where('event', 'LIKE', "$event%")
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(
            [
                'success' => true,
                'data' => $attendance
            ], 200
        );
    }

    public function syncAttendance($indexNumber, Request $request) {
        $student = Student::where('index_number', $indexNumber)->first();
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => 'Student Not Found! ID is not valid. Please check and try again.'
            ], 404
        );

        $unsyncedData = $request->get('unsyncedData');

        AnagkazoAttendance::synchronizeAttendance($unsyncedData);

    }

    public function exportAttendance(Request $request) {

        $from = $request->get('fromdate');
        $to = $request->get('todate');

        $fromBeginning = $request->get("frombeginning", false);

        $calculate_point = $request->get("calculate_point", false);

        $event = $request->get('event');
        $classId = $request->get('classes');

        list($dateFrom, $dateTo) = $this->getExportDateRange($from, $to, $event, $fromBeginning, $classId);

        return (new GenerateCSVAttendanceReport($dateFrom, $dateTo, $event, $classId, $calculate_point))->downloadCSV("$event-$classId-export.csv");

    }

    public function exportAttendance_halt(Request $request) {

        $from = $request->get('fromdate');
        $to = $request->get('todate');

        $fromBeginning = $request->get("frombeginning", false);

        $calculate_point = $request->get("calculate_point", false);

        $event = $request->get('event');
        $classId = $request->get('classes');

        list($dateFrom, $dateTo) = $this->getExportDateRange($from, $to, $event, $fromBeginning, $classId);

        return (new GenerateAttendanceReport($dateFrom, $dateTo, $event, $classId, $calculate_point))->download("$event-$classId-export.xlsx");

    }

    public function getExportDateRange($fromDate, $toDate, $event, $fromBeginning = false, $classId = 'all')
    {
        if (!$fromBeginning) {
            return [$fromDate, $toDate];
        }

        $dateFrom = AnagkazoAttendance::getStartingDate($event, $classId);
        $dateTo = AnagkazoAttendance::getEndDate($event, $classId);

        return [$dateFrom, $dateTo];
    }

    public function classes() {
        return StudentClass::all();
    }

    public function postFellowshipService($studentId, Request $request) {
        Log::info(["Request: " => $request->all()]);

        $student = Student::find($studentId);
        if (!$student) return response()->json(
            [
                'success' => false,
                'message' => "Could not save fellowship service. Student doesn't exist"
            ], 400
        );

        $uploadedFileUrl = null;

        //get uploaded image from request
        if ($request->file('fellowship_service_image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('fellowship_service_image')->getRealPath(), [
                'folder' => 'Anagkazo.Apps'
            ])->getSecurePath();
        }else{
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Could not save fellowship service Image.'
                ], 400
            );
        }

        $result = FellowshipService::updateOrCreate(
            [
                'student_id' => $student->id,
                'service_date' => $request->get('service_date'),
            ],
            [
                'attendance' => $request->get('attendance'),
                'offering' => $request->get('offering'),
                'foreign_offering' => $request->get('foreign_offering'),
                'image_url' => $uploadedFileUrl
            ]
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Fellowship Data Saved',
                'data' => json_encode([
                    'service_date' => $result->service_date,
                    'image_url' => $result->image_url,
                    'attendance' => $result->attendance,
                    'offering' => $result->offering,
                    ])
            ]
            );
    }

    public function getFellowshipService($studentId, Request $request){
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Cannot get student'
                ]
            );
        }

        $fellowshipServices = FellowshipService::where('student_id', $student->id)->orderBy('service_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $fellowshipServices
        ]);
    }

    public function cancelFellowshipService($studentId, Request $request) {
        $student = Student::find($studentId);

        if (!$student) return;

        $result = FellowshipService::updateOrCreate(
            [
                'student_id' => $student->id,
                'service_date' => $request->get('service_date'),
            ],
            [
                'cancel_service_reason' => $request->get('reason')
            ]
        );

        if ($result) {
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Service has been cancelled successfully!',
                ]
                );
        }else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Service could not be cancelled!'
                ]
                );
        }

    }

}
