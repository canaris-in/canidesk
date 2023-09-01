<?php

namespace App\Http\Controllers;
use App\Conversation;
use Illuminate\Http\Request;
use App\SLASetting;
use Illuminate\Support\Facades\DB;
use Mail;
use Dompdf\Dompdf;
use Dompdf\Options;
use \PDF;


class ReportSettingsController extends Controller
{
    public function index(){

    $settings=SLASetting::orderBy('id', 'desc')->first();
    if (empty($settings)) {
        $settings = (object) [
            "to_email" => "support@gmail.com",
            "frequency" => "Daily",
            "schedule" => "Monday",
            "auto_data" => "1",
            "time" => "12:10:00"
        ];
        return view('sla.settings',compact('settings'));
    } else {
        return view('sla.settings',compact('settings'));
    }

}

    public function addDataSettings(Request $request){
    $slaSettings = new SLASetting();
    $slaSettings->to_email=$request->to_email;

    $slaSettings->frequency=$request->frequency;
    if($request->frequency==="Daily"){
        $slaSettings->schedule='null';
    }else{
        $slaSettings->schedule=$request->schedule;
    }

    $slaSettings->time=$request->time;
    $auto=$request->auto_data;
    if($auto==""){
        $slaSettings->auto_data="0";
    }else{
        $slaSettings->auto_data=$request->auto_data;
    }
    $slaSettings->save();
    return redirect('/reports/settings');
   }

}
