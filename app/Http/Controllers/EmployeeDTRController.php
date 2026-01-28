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
use App\Models\Employee;
use App\Models\AdminUsers;
use App\Models\EmployeeDTR;
use App\Models\EmployeeRate;
use App\Models\PayrollPeriod;
use App\Models\Branch;
use App\Models\BranchSite;

class EmployeeDTRController extends Controller {
 
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
        Session::flash('session_expires','Your session has expired. Please log-in again.');
        return false;
    }

    return true;
  }

public function showAdminDTRUploader(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee DTR';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

  return View::make('admin/admin-employee-dtr-uploader')->with($data);

}

// FINAL DTR TABLE
  public function getEmployeeDTRList(Request $request){

    $EmployeeDTR = new EmployeeDTR();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');
    $param["cutoffid"] = Session('ADMIN_PAYROLL_PERIOD_SCHED_ID') ?? 0;
    
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeDTRList"] = $EmployeeDTR->getEmployeeDTRList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $EmployeeDTRList = $EmployeeDTR->getEmployeeDTRList($param);
    $RetVal["TotalRecord"] = count($EmployeeDTRList);

    return response()->json($RetVal);

  }

  public function getEmployeeDTRInfo(Request $request){

    $EmployeeDTR = new EmployeeDTR();

    $PlatformType=request("Platform");
    $DTR_ID = request("DTR_ID");

    $EmployeeDTRInfo = $EmployeeDTR->getEmployeeDTRInfo($DTR_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeDTRInfo"] = $EmployeeDTRInfo;

    return response()->json($RetVal);
  }

  public function doSaveUploadFinalDTRDTransaction(){

    $EmployeeDTR = new EmployeeDTR();

    $data["SearchText"] = '';
    $data["Status"] = 'Pending';
    $data["PageNo"] = '';
    $data["Limit"] = '';

    $RetVal = $EmployeeDTR->doSaveUploadFinalDTRDTransaction($data);

    return response()->json($RetVal);

  }

   public function doSetDTRTransactionStatus(Request $request){

    $Employee = new Employee();
    $EmployeeDTR = new EmployeeDTR();
    $PayrollPeriod= new PayrollPeriod();
    
    $ResponseMessage = "";
    $RetVal['Response'] = "Failed";
    $RetVal['DuplicateDTR'] = false;
    
    $data["DTRID"] = request('DTR_ID'); 
    $data["NewStatus"] = request('NewStatus');

    $data["CURRENT_DTR_ID"]=$data["DTRID"];

    if($data["NewStatus"]=='Approved'){

         $info=$EmployeeDTR->getEmployeeDTRInfo($data["DTRID"]);
         if(isset($info)>0){

             $data["EmpID"]=$info->EmployeeID;             
             $data["RateID"]=$info->EmployeeRateID;
             $data["PayrollID"]=$info->PayrollPeriodID;
             $data["PayrollCode"]=$info->PayrollPeriodCode;
             
                if($EmployeeDTR->doCheckEmployeeDTRIfExist($data)){           
                   $RetVal['Response'] = "Failed";
                   $RetVal['DuplicateDTR'] = true;
                   $ResponseMessage= "Record for this Employee has existing Approved DTR for payroll period ".$data["PayrollCode"].".";
                }else{                  
                   if($data["EmpID"]==0){
                     $RetVal['Response'] = "Failed";
                     $RetVal['DuplicateDTR'] = false;
                     $ResponseMessage= "Please edit & review DTR & select valid employee.";
                    }else if($data["RateID"]==0){
                     $RetVal['Response'] = "Failed";
                     $RetVal['DuplicateDTR'] = false;
                     $ResponseMessage= "Please edit & review DTR & set up employee rate. ";  
                   }else{
                     $RetVal['Response'] = "Success";
                     $RetVal['DuplicateDTR'] = false;
                     $ResponseMessage = "Employee DTR has set to approved successfully.";
                   }                   
              }

       }else{
             $RetVal['Response'] = "Success";
             $RetVal['DuplicateDTR'] = false;
             $ResponseMessage = "Employee DTR has set to approved successfully.";
      }
    
    }else if($data["NewStatus"]=='Cancelled'){
        $RetVal['Response'] = "Success";
        $RetVal['DuplicateDTR'] = false;
        $ResponseMessage = "Employee DTR has set to cancelled successfully.";
    }else{
      $RetVal['Response'] = "Success";
      $ResponseMessage = "Employee DTR has set to approved successfully.";
    }
   
   if($RetVal['Response']!= "Success"){        
         $RetVal['ResponseMessage'] = $ResponseMessage;
         $RetVal["EmployeeDTRInfo"] = null;
    }else{         
         $EmployeeDTR->doSetDTRTransactionStatus($data);
         $RetVal['ResponseMessage'] = $ResponseMessage;
         $RetVal["EmployeeDTRInfo"] =  $EmployeeDTR->getEmployeeDTRInfo($data["CURRENT_DTR_ID"]);
    }

    return response()->json($RetVal);
  }
  
   public function doSaveEmployeeDTR(Request $request){
       
    $Employee = new Employee();
    $EmployeeRate = new EmployeeRate();
    $EmployeeDTR = new EmployeeDTR();
    $PayrollPeriod= new PayrollPeriod();

    $RetVal['DuplicateDTR'] = false;

    $data["EmpID"]=0;
    $data["PayrollID"]=0;
    $data["EmployeeRateID"]=0;

    $ResponseMessage = "";
    $data["DTRID"] =  request('DTR_ID');

    $data["PayrollCode"] =  request("PayrollCode");
    $payroll_info = $PayrollPeriod->getPayrollPeriodScheduleInfoByCode($data["PayrollCode"]);
    if(isset($payroll_info)>0){
       $data["PayrollID"]=$payroll_info->ID;
    }

    $data["EmpNo"] =  request("EmpNo");
    $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$data["EmpNo"]);
    if(isset($emp_info)>0){
       $data["EmpID"]=$emp_info->employee_id;
    }  
    
    $data["Year"] =  request("Year");
    $data["TransDate"] =  request("TransDate");

    $data["EmpNo"] =  request("EmpNo");
    $data["EmpRate"] =  request("EmpRate");
    $rate_info = $EmployeeRate->getEmployeeRateInfo('ByEmployeeNo',$data["EmpNo"]);
    if(isset($rate_info)>0){
       $data["EmployeeRateID"]=$rate_info->EmployeeRateID;
       $data["EmpRate"] =  $rate_info->HourlyRate;
    }
   
     if($EmployeeDTR->doCheckEmployeeDTRIfExist($data)){
        $RetVal['DuplicateDTR'] = true;
        $ResponseMessage= "Selected Employee has already Aprroved DTR for payroll period ".$data["PayrollCode"].".";
     }
  
    $data["RegHours"] =  request("RegHours");
    $data["LateHours"] =  request("LateHours");
    $data["UnderTimeHours"] =  request("UnderTimeHours");
    $data["NDHours"] =  request("NDHours");
    $data["Absent"] =  request("Absent");

    $data["Leave01"] =  request("Leave01");
    $data["Leave02"] =  request("Leave02");
    $data["Leave03"] =  request("Leave03");
    $data["Leave04"] =  request("Leave04");
    $data["Leave05"] =  request("Leave05");
    $data["Leave06"] =  request("Leave06");
    $data["Leave07"] =  request("Leave07");
    $data["Leave08"] =  request("Leave08");
    $data["Leave09"] =  request("Leave09");
    $data["Leave10"] =  request("Leave10");
    $data["Leave11"] =  request("Leave11");
    $data["Leave12"] =  request("Leave12");
    $data["Leave13"] =  request("Leave13");
    $data["Leave14"] =  request("Leave14");
    $data["Leave15"] =  request("Leave15");
    $data["Leave16"] =  request("Leave16");
    $data["Leave17"] =  request("Leave17");
    $data["Leave18"] =  request("Leave18");
    $data["Leave19"] =  request("Leave19");
    $data["Leave20"] =  request("Leave20");

    $data["OTHours01"] =  request("OTHours01");
    $data["OTHours02"] =  request("OTHours02");
    $data["OTHours03"] =  request("OTHours03");
    $data["OTHours04"] =  request("OTHours04");
    $data["OTHours05"] =  request("OTHours05");
    $data["OTHours06"] =  request("OTHours06");
    $data["OTHours07"] =  request("OTHours07");
    $data["OTHours08"] =  request("OTHours08");
    $data["OTHours09"] =  request("OTHours09");
    $data["OTHours10"] =  request("OTHours10");
    $data["OTHours11"] =  request("OTHours11");
    $data["OTHours12"] =  request("OTHours12");
    $data["OTHours13"] =  request("OTHours13");
    $data["OTHours14"] =  request("OTHours14");
    $data["OTHours15"] =  request("OTHours15");
    $data["OTHours16"] =  request("OTHours16");
    $data["OTHours17"] =  request("OTHours17");
    $data["OTHours18"] =  request("OTHours18");
    $data["OTHours19"] =  request("OTHours19");
    $data["OTHours20"] =  request("OTHours20");     
    $data["OTHours21"] =  request("OTHours21");
    $data["OTHours22"] =  request("OTHours22");
    $data["OTHours23"] =  request("OTHours23");
    $data["OTHours24"] =  request("OTHours24");
    $data["OTHours25"] =  request("OTHours25");

    $data["IsUploaded"] =  request("IsUploaded");
           
    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeDTRInfo"] = null;

      }else{

        $DTR_ID = $EmployeeDTR->doSaveEmployeeDTR($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee DTR has saved successfully.";
        $RetVal["EmployeeDTRInfo"] =  $EmployeeDTR->getEmployeeDTRInfo($DTR_ID);

      }
  
    return response()->json($RetVal);

  }

function doCheckPayrollPeriodExistInDTR(){

    $EmployeeDTR = new EmployeeDTR();
    $ResponseMessage = "";
 
    $data["PayrollPeriodCode"] =  request("PayrollPeriodCode");
    $data["PayrollPeriodYear"] =  request("PayrollPeriodYear");
    $EmployeeDTR->checkIfReUplodedDTRPayrollPeriod($data);
}

  //TEMP TABLE
  public function getDTRTempList(Request $request){

    $EmployeeDTR = new EmployeeDTR();

    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["DTRTempList"] = $EmployeeDTR->getDTRTempList($param);

    //For Error Record
    $RetVal['TotalError']=0;
    $RetVal['TotalError']=DB::table('payroll_dtr_temp')->where('IsUploadError','>',0)->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    //For Paging
    $RetVal['TotalRecord']=0;
    $RetVal['TotalRecord']=DB::table('payroll_dtr_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);

  }

    public function getDTRTempInfo(Request $request){

    $EmployeeDTR = new EmployeeDTR();

    $PlatformType=request("Platform");
    $DTR_ID = request("DTR_ID");

    $EmployeeDTRInfo = $EmployeeDTR->getDTRTempInfo($DTR_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeDTRInfo"] = $EmployeeDTRInfo;

    return response()->json($RetVal);
  }

   public function doSaveDTRTempTransaction(Request $request){
       
    $Employee = new Employee();
    $EmployeeDTR = new EmployeeDTR();
    $PayrollPeriod= new PayrollPeriod();

    $data["EmpID"]=0;
    $data["PayrollID"]=0;

    $ResponseMessage = "";
    $data["DTRID"] =  request('DTR_ID');

    $data["PayrollCode"] =  request("PayrollCode");
    $payroll_info = $PayrollPeriod->getPayrollPeriodScheduleInfoByCode($data["PayrollCode"]);
    if(isset($payroll_info)>0){
       $data["PayrollID"]=$payroll_info->ID;
    }

    $data["EmpNo"] =  request("EmpNo");
    $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$data["EmpNo"]);
    if(isset($emp_info)>0){
       $data["EmpID"]=$emp_info->employee_id;
    }  

    $data["Year"] =  request("Year");
    $data["TransDate"] =  request("TransDate");
    $data["EmpRate"] =  request("EmpRate");
    $data["RegHours"] =  request("RegHours");
    $data["LateHours"] =  request("LateHours");
    $data["UnderTimeHours"] =  request("UnderTimeHours");
    $data["NDHours"] =  request("NDHours");
    $data["Absent"] =  request("Absent");

    $data["Leave01"] =  request("Leave01");
    $data["Leave02"] =  request("Leave02");
    $data["Leave03"] =  request("Leave03");
    $data["Leave04"] =  request("Leave04");
    $data["Leave05"] =  request("Leave05");
    $data["Leave06"] =  request("Leave06");
    $data["Leave07"] =  request("Leave07");
    $data["Leave08"] =  request("Leave08");
    $data["Leave09"] =  request("Leave09");
    $data["Leave10"] =  request("Leave10");
    $data["Leave11"] =  request("Leave11");
    $data["Leave12"] =  request("Leave12");
    $data["Leave13"] =  request("Leave13");
    $data["Leave14"] =  request("Leave14");
    $data["Leave15"] =  request("Leave15");
    $data["Leave16"] =  request("Leave16");
    $data["Leave17"] =  request("Leave17");
    $data["Leave18"] =  request("Leave18");
    $data["Leave19"] =  request("Leave19");
    $data["Leave20"] =  request("Leave20");

    $data["OTHours01"] =  request("OTHours01");
    $data["OTHours02"] =  request("OTHours02");
    $data["OTHours03"] =  request("OTHours03");
    $data["OTHours04"] =  request("OTHours04");
    $data["OTHours05"] =  request("OTHours05");
    $data["OTHours06"] =  request("OTHours06");
    $data["OTHours07"] =  request("OTHours07");
    $data["OTHours08"] =  request("OTHours08");
    $data["OTHours09"] =  request("OTHours09");
    $data["OTHours10"] =  request("OTHours10");
    $data["OTHours11"] =  request("OTHours11");
    $data["OTHours12"] =  request("OTHours12");
    $data["OTHours13"] =  request("OTHours13");
    $data["OTHours14"] =  request("OTHours14");
    $data["OTHours15"] =  request("OTHours15");
    $data["OTHours16"] =  request("OTHours16");
    $data["OTHours17"] =  request("OTHours17");
    $data["OTHours18"] =  request("OTHours18");
    $data["OTHours19"] =  request("OTHours19");
    $data["OTHours20"] =  request("OTHours20");     
    $data["OTHours21"] =  request("OTHours21");
    $data["OTHours22"] =  request("OTHours22");
    $data["OTHours23"] =  request("OTHours23");
    $data["OTHours24"] =  request("OTHours24");
    $data["OTHours25"] =  request("OTHours25");

     $data["IsUploaded"] =  request("IsUploaded");
     $data["Status"] =  request("Status");        
          
      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["DTRTempInfo"] = null;

      }else{

        $DTR_ID = $EmployeeDTR->doSaveDTRTempTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "DTR information has saved successfully.";
        $RetVal["DTRTempInfo"] =  $EmployeeDTR->getDTRTempInfo($DTR_ID);
      }
  
    return response()->json($RetVal);

  }

public function doRemoveDuplicateDTRTempTransaction(Request $request){

  $EmployeeDTR = new EmployeeDTR();
  $ResponseMessage = "";

  $tempID=request("tempID");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["DTRTempInfo"] = null;

  }else{

  $EmployeeDTR->doRemoveDuplicateDTRTempTransaction($tempID);

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "Employee with duplicate DTR entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

public function doSaveDTRTempTransactionPerBatch(Request $request){

  $EmployeeDTR = new EmployeeDTR();
  $ResponseMessage = "";

  $data['DTRTempDataItems']=request("DTRTempDataItems");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["DTRTempInfo"] = null;

  }else{

  $EmployeeDTR->doSaveDTRTempTransactionPerBatch($data);
  $RetVal['Response'] = "Success";
  
  }
  
  return response()->json($RetVal);

}

 public function doClearDTRTempTransaction(Request $request){

  //Clear Temp Table 
  $PlatformType=request("Platform");      
  DB::table('payroll_dtr_temp')
        ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
        ->delete();

  }

  public function getTempDTRTransactionCount(){

    $RetVal['MaxCount']=0;
    $RetVal['MaxCount']=DB::table('payroll_dtr_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);
  }
  
}



