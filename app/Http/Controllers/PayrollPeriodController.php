<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Mail;
use Session;
use Hash;
use View;
use Image;
use DB;
use Excel;
use PDF;

use App\Models\Misc;
use App\Models\AdminUsers;
use App\Models\PayrollPeriod;

class PayrollPeriodController extends Controller {
 
  function SetAdminInitialData($data){
    
    $Page=$data['Page'];
    $AdminUsers = new AdminUsers();  

    $data['Allow_View_Print_Export']=0;
    $data['Allow_Add_Create_Import_Upload']=0;
    $data['Allow_Edit_Update']=0;
    $data['Allow_Delete_Cancel']=0;
    $data['Allow_Post_UnPost_Approve_UnApprove']=0;

    if($Page!='' && ($Page!='Admin Dashboard' || $Page!='Forgot Password')){

      $hasMenuAccess=0;

      if($AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),$data['Page'])){
        $hasMenuAccess=1;

        $RetVal=$AdminUsers->getAdminUserAccessMenuDetails(Session::get('ADMIN_USER_ID'),$data['Page']);

        $data['Allow_View_Print_Export'] = $RetVal['Allow_View_Print_Export'];
        $data['Allow_Add_Create_Import_Upload'] =  $RetVal['Allow_Add_Create_Import_Upload'];
        $data['Allow_Edit_Update'] =  $RetVal['Allow_Edit_Update'];
        $data['Allow_Delete_Cancel'] =  $RetVal['Allow_Delete_Cancel'];
        $data['Allow_Post_UnPost_Approve_UnApprove'] =  $RetVal['Allow_Post_UnPost_Approve_UnApprove'];

       }else{
          // if no access then go to admin dashboard
          //return Redirect::route('admin-dashboard');
       }
     }
       
     $data["AdminUsers"]=$AdminUsers;

     return $data;
  }

  function IsAdminLoggedIn(){

    if (!Session('ADMIN_LOGGED_IN')) {
        Session::flash('session_expires','Your session has been expired. Please log-in again.');
        return false;
    }

    return true;
  }


public function showAdminPayrollPeriodSchedule(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Period Schedule';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-payroll-period-schedule')->with($data);

}

  public function getPayrollPeriodSheduleList(Request $request){

    $PayrollPeriod = new PayrollPeriod();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $PayrollPeriodList = $PayrollPeriod->getPayrollPeriodList($param);
    $RetVal["TotalRecord"] = count($PayrollPeriodList);
    
    return response()->json($RetVal);

  }

  public function getPayrollScheduleInfo(Request $request){

    $PayrollPeriod = new PayrollPeriod();

    $PlatformType=request("Platform");
    $PayrollPeriodScheduleID = request("PayrollScheduleID");

    $PayrollPeriodScheduleInfo = $PayrollPeriod->getPayrollPeriodScheduleInfo($PayrollPeriodScheduleID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollPeriodScheduleInfo"] = $PayrollPeriodScheduleInfo;

    return response()->json($RetVal);
  }

   public function doSavePayrollPeriodSchedule(Request $request){
       
    $PayrollPeriod = new PayrollPeriod();

    $ResponseMessage = "";
    $data["PayrollScheduleID"] =  request('PayrollScheduleID');
    $data["PayrollScheduleCode"] =    str_replace(' ','',request('PayrollScheduleCode'));
    $data["PayrollScheduleRemarks"] =  request("PayrollScheduleRemarks");
    $data["PayrollStartDate"] =  request("PayrollStartDate");
    $data["PayrollEndDate"] =  request("PayrollEndDate");
    $data["CutOff"] =  request("PayrollCutOff");
    $data["Year"] =  request("PayrollYear");
    $data["Status"] =  request("Status");

    if(strlen($data["PayrollScheduleCode"])>10){
       $ResponseMessage= "Payroll schedule code must not exceeds in 10 characters.";
    }

    if($PayrollPeriod->doCheckPayrollPeriodCodeIfExist($data)){
        $ResponseMessage= "Payroll period code is already used.";
    }

     if($PayrollPeriod->doCheckPayrollPeriodDatesIfExist($data)){
      $ResponseMessage= "Payroll period date from and date to is already used.";
     }

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["PayrollPeriodScheduleInfo"] = null;

      }else{

        $PayrollScheduleID = $PayrollPeriod->doSavePayrollPeriod($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Payroll period schedule has saved successfully.";
        $RetVal["PayrollPeriodScheduleInfo"] =  $PayrollPeriod->getPayrollPeriodScheduleInfo($PayrollScheduleID);

      }
  
    return response()->json($RetVal);

  }

public function getPayrollPeriodSearchList(Request $request){

    $PayrollPeriod = new PayrollPeriod();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Open';

    $PayrollPeriodList = $PayrollPeriod->getPayrollPeriodList($param);

    $RetVal =array();
    foreach($PayrollPeriodList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Code.'|'.
        $row->Year.'|'.
        $row->StartDateFormat.'|'.
        $row->EndDateFormat;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  

}

}



