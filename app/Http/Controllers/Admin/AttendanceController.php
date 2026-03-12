<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Symfony\Component\HttpFoundation\Response;
use Gate;
class AttendanceController extends Controller
{
    public function index(){
        abort_if(Gate::denies('attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schedules = Schedule::where('is_workshop','1')->get();
        return view('admin.attendances.index',compact('schedules'));
    }
    public function show($id){
        abort_if(Gate::denies('attendance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schedule = Schedule::with('users')->find($id);
        return view('admin.attendances.show',compact('schedule'));
    }

    function updateAttendance(Request $request){

        abort_if(Gate::denies('attendance_taken'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userId = $request->input('userId');
        $scheduleId = $request->input('scheduleId');
        $attendanceStatus = $request->input('attendanceStatus');

        try {
            $attendance = Attendance::where('user_id', $userId)
                ->where('schedule_id', $scheduleId)
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->user_id = $userId;
                $attendance->schedule_id = $scheduleId;
            }

            $attendance->attendance_status = $attendanceStatus;
            $attendance->save();

            return response()->json(['message' => 'Attendance updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating attendance'], 500);
        }


//        if($request->ajax()){

//           // $data = Schedule::find($request->idea_id);
//            if (!is_null($data)) {
//                if ($data->status==1){
////                    $data->status = 0;
////                    $data->save();
//                    return "Absent";
//                }else{
////                    $data->status = 1;
////                    $data->save();
//                    return "Present";
//                }
//            }
//        }
    }
public function downloadCertificate($id){
    abort_if(Gate::denies('attendance_certificate'), Response::HTTP_FORBIDDEN, '403 Forbidden');
       $attendance = Attendance::where('schedule_id',$id)
           ->where('attendance_status','1')
           ->where('user_id',auth()->user()->id)
           ->first();

        if($attendance){
            $schedule = Schedule::find($id);
            $pdf = \PDF::loadView('admin.attendances.certificate',compact('schedule','attendance'));
            return $pdf->download('certificate.pdf');
        }
//        $schedules = Schedule::where('is_workshop','1')->get();
//        return view('admin.attendances.download',compact('schedules'));
}

public function eventAttendance(){
    abort_if(Gate::denies('attendance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $profiles = Profile::where('payment_status','1')->get();
    return view('admin.attendances.total-participant',compact('profiles'));
}

    function eventAttendanceTotal(Request $request){


        //abort_if(Gate::denies('attendance_taken'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $profileId = $request->input('userId');
        $attendanceStatus = $request->input('attendanceStatus');
        $data=[];
        try {
            $profile = Profile::where('id', $profileId)
                ->first();
            if ($attendanceStatus==0) {
                $data['event_attendance']='0';
            }else{
                $data['event_attendance']='1';
            }
            $profile->update($data);
            return response()->json(['message' => 'Attendance updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating attendance'], 500);
        }

    }


}
