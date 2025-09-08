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
use App\Models\Branch;
use App\Models\Employee;
use App\Models\BranchSite;
use App\Models\AdminUsers;
use App\Models\EmployeeRate;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeMP2Contribution;

class EmployeeController extends Controller {
 
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
        Session::flash('session_expires','You have been idle for several minutes & your session has been expired.Please log-in again');
        return false;
    }

    return true;
  }


public function showAdminEmployee(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee List';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

  return View::make('admin/admin-employee-list')->with($data);

}

 public function getEmployeeList(Request $request){

    $Employee = new Employee();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeList"] = $Employee->getEmployeeList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $EmployeeList = $Employee->getEmployeeList($param);
    
    $RetVal["TotalRecord"] = count($EmployeeList);

    return response()->json($RetVal);

  }

  public function getEmployeeInfo(Request $request){

    $Employee = new Employee();

    $PlatformType=request("Platform");
    $EmployeeID = request("EmployeeID");

    $EmployeeInfo = $Employee->getEmployeeInfo('ByEmployeeID',$EmployeeID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeInfo"] = $EmployeeInfo;

    return response()->json($RetVal);
  }

  public function getEmployeeSearchList(Request $request){

    $Employee = new Employee();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Active';

    $EmployeeList = $Employee->getEmployeeList($param);

    $RetVal =array();
    foreach($EmployeeList as $row)
    { 

  $data = $row->employee_id.'|'.
        $row->employee_number.'|'.
        $row->first_name.'|'.
        $row->last_name.'|'.
        $row->middle_name.'|'.
        $row->FullName.'|'.
        $row->MobileNo.'|'.
        $row->EmailAddress.'|'.
        $row->BranchID.'|'.
        $row->BranchName;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }


  //================================================
  //
  //EMPLOYEE RATE
   public function getEmployeeRateList(Request $request){

    $EmployeeRate = new EmployeeRate();

    $param["EmployeeID"] = request("EmployeeID");  
    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = $request["Status"];
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeRateList"] = $EmployeeRate->getEmployeeRateListByEmployeeID($param);

    return response()->json($RetVal);

  }

   public function getEmployeeRateInfo(Request $request){

    $EmployeeRate = new EmployeeRate();

    $PlatformType=request("Platform");
    $EmployeeRateID = request("EmployeeRateID");

    $EmployeeRateInfo = $EmployeeRate->getEmployeeRateInfo('ByID',$EmployeeRateID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeRateInfo"] = $EmployeeRateInfo;

    return response()->json($RetVal);
  }

  function doSaveEmployeeRate(Request $request){

    $Employee = new Employee();  
    $EmployeeRate = new EmployeeRate();

    $ResponseMessage = "";
    $data["EmployeeRateID"] =  request("EmployeeRateID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["EffectivityDate"] =  request("EffectivityDate");

    $data["MonthlyRate"] =  request("MonthlyRate");
    $data["DailyRate"] =  request("DailyRate");
    $data["HourlyRate"] =  request("HourlyRate");

    $data["RateRemarks"] =  request("RateRemarks");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;

      }else{

        $EmployeeRate_ID = $EmployeeRate->doSaveEmployeeRate($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee rate has saved successfully.";
        $RetVal["EmployeeInfo"] =  $Employee->getEmployeeInfo('ByEmployeeID',$data["EmployeeID"]);
        $RetVal["EmployeeRateInfo"] =  $EmployeeRate->getEmployeeRateInfo('ByEmployeeID',$EmployeeRate_ID);
                
      }
  
    return response()->json($RetVal);

  }
  
  //EMPLOYEE SET NEW HDMF CONTRIBUTION
  function doUpdateEmployeeNewHMDFContribution(Request $request){

    $Employee = new Employee();  
  
    $ResponseMessage = "";    
    $data["EmployeeID"] =  request("EmployeeID");    

    $data["HDMF_New_EE"] =  request("HDMF_New_EE");
    $data["HDMF_New_ER"] =  request("HDMF_New_ER");
    
    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;

      }else{

        $EmployeeRate_ID = $Employee->doUpdateEmployeeNewHMDFContribution($data);
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee New HDMF Contribution has saved successfully.";
        $RetVal["EmployeeInfo"] =  $Employee->getEmployeeInfo('ByEmployeeID',$data["EmployeeID"]);        
      }
  
    return response()->json($RetVal);

  }
  
  //EMPLOYEE MP2 CONTRIBUTION
  function doSaveUpdateEmployeeMP2Contribution(Request $request){

    $Employee = new Employee(); 
    $EmployeeMP2Contribution = new EmployeeMP2Contribution();      

    $ResponseMessage = "";    
    
    $data["EmployeeID"] =  request("EmployeeID");    
    $data["MP2AccountNo"] =  request("MP2AccountNo");
    $data["MP2ContributionAmount"] =  request("MP2ContributionAmount");
    $data["MP2Frequency"] =  request("MP2Frequency");
    
    if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;
      }else{
        $Employee_MP2_ID = $EmployeeMP2Contribution->doSaveUpdateEmployeeMP2Contribution($data);
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee new MP2 contribution has saved successfully.";
        $RetVal["EmployeeInfo"] =  $Employee->getEmployeeInfo('ByEmployeeID',$data["EmployeeID"]);        
      }
  
    return response()->json($RetVal);

  }

  public function doClearMP2TempUpload(Request $request){

     //Clear MP2 Temp 
      $PlatformType=request("Platform");
      DB::table('payroll_employee_mp2_setup_temp')
              ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
              ->delete();
  }

 public function getTempMP2UploadCount(){

    $MaxCount=0;
    $MaxCount=DB::table('payroll_employee_mp2_setup_temp')
                       ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
                       ->count();

   $RetVal['MaxCount']=$MaxCount;
   
    return response()->json($RetVal);

  }
 
  //MP2 TEMP CONTRIBUTION
  public function getEmployeeTempMP2List(Request $request){

    $EmployeeMP2Contribution = new EmployeeMP2Contribution();

    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["MP2TempList"] = $EmployeeMP2Contribution->getEmployeeTempMP2List($param);

    //For Paging
    $RetVal['TotalRecord']=0;
    $RetVal['TotalRecord']=DB::table('payroll_employee_mp2_setup_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);

  }
  
  public function doSaveEmployeeTempMP2Batch(Request $request){

  $EmployeeMP2Contribution = new EmployeeMP2Contribution();
  $ResponseMessage = "";

  $data['TempMP2DataItems']=request("TempMP2DataItems");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["EmployeeMP2TempInfo"] = null;

  }else{

  $EmployeeMP2Contribution->doSaveEmployeeTempMP2Batch($data);

  $RetVal['Response'] = "Success";
  // $RetVal['ResponseMessage'] = "Employee rate is sucessfully uploaded.";
  }
  return response()->json($RetVal);
 }

 public function doRemoveDuplicateTempMP2Upload(Request $request){

  $EmployeeMP2Contribution = new EmployeeMP2Contribution();
  $ResponseMessage = "";

  $tempID=request("tempID");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["RateTempList"] = null;

  }else{

  $EmployeeMP2Contribution->doRemoveDuplicateTempMP2Upload($tempID);

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "Employee with duplicate MP2 entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

public function getEmployeeTempMP2Info(Request $request){

    $EmployeeMP2Contribution = new EmployeeMP2Contribution();

    $PlatformType=request("Platform");
    $EmployeeMP2ID = request("EmployeeMP2ID");

    $EmployeeTempMP2Info = $EmployeeMP2Contribution->getEmployeeTempMP2Info($EmployeeMP2ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeTempMP2Info"] = $EmployeeTempMP2Info;

    return response()->json($RetVal);
  }

 public function doSaveUploadEmployeeMP2(Request $request){

   $EmployeeMP2Contribution = new EmployeeMP2Contribution();

    $data["SearchText"] = '';
    $data["Status"] = 'Inactive';
    $data["PageNo"] = '';
    $data["Limit"] = '';

    $RetVal = $EmployeeMP2Contribution->doSaveUploadEmployeeMP2($data);

    return response()->json($RetVal);

  }

function doSaveEmployeeTempMP2(Request $request){

   $EmployeeMP2Contribution = new EmployeeMP2Contribution();

    $ResponseMessage = "";
    $data["EmployeeTempMP2ID"] =  request("EmployeeTempMP2ID");
    $data["EmployeeID"] =  request("EmployeeID");

    $data["MP2AccountNo"] =  request("MP2AccountNo");
    $data["MP2DeductionAmount"] =  request("MP2DeductionAmount");    
    $data["MP2Frequency"] =  request("MP2Frequency");
    $data["MP2Frequency"] =  request("MP2Frequency");
    
    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;

      }else{

        $EmployeeMP2ID = $EmployeeMP2Contribution->doSaveEmployeeTempMP2($data);
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee uploaded MP2 has saved successfully.";
                
      }
  
    return response()->json($RetVal);
  }

  //EMPLOYEE ALLOWANCE SET UP
  function doSaveUpdateEmployeeAllowance(Request $request){

    $EmployeeAllowance = new EmployeeAllowance();      

    $ResponseMessage = "";    
    $data["EmployeeID"] =  request("EmployeeID");
    $data["EmployeeAllowanceItems"] =  request("EmployeeAllowanceItems");
    $data["ItemCount"] =  request("ItemCount");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;

      }else{

        $EmployeeAllowance->doSaveUpdateEmployeeAllowance($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee Allowances has saved successfully.";  

        $EmployeeAllowanceInfo=$EmployeeAllowance->getEmployeeAllowanceList($data["EmployeeID"]);          
        $RetVal['EmployeeAllowanceInfo']=$EmployeeAllowanceInfo;     
                
      }
  
    return response()->json($RetVal);

  }

  function getEmployeeAllowanceSetUpList(Request $request){

    $EmployeeAllowance = new EmployeeAllowance();      

    $ResponseMessage = "";   
    $RetVal['Response'] = "Success"; 
    $data["EmployeeID"] =  request("EmployeeID");

    $RetVal['EmployeeAllowanceInfo']=$EmployeeAllowance->getEmployeeAllowanceList($data["EmployeeID"]);            
    return response()->json($RetVal);

  }

 function getEmployeeRateID(Request $request){

  $EmployeeRate = new EmployeeRate();
   
   $EmployeeRateID=0;
   $MonthlyRate=0;
   $DailyRate=0;
   $HourlyRate=0;

   $EmployeeID= request("EmployeeID");

   $RetVal['Response'] = "Success";
   $RetVal['ResponseMessage'] = "";

   $info = $EmployeeRate->getEmployeeRateInfo('ByEmployeeID',$EmployeeID);

    if(isset($info)>0){
        $EmployeeRateID=$EmployeeRateID=$info->EmployeeRateID;
        $MonthlyRate=$info->MonthlyRate;
        $DailyRate=$info->DailyRate;
        $HourlyRate=$info->HourlyRate;
    }

    $RetVal['EmployeeRateID']=$EmployeeRateID;
    $RetVal['MonthlyRate']=$MonthlyRate;
    $RetVal['DailyRate']=$DailyRate;
    $RetVal['HourlyRate']=$HourlyRate;

    return response()->json($RetVal);

 }

 public function doSaveUploadEmployeeRates(Request $request){

  $EmployeeRate = new EmployeeRate();

    $data["SearchText"] = '';
    $data["Status"] = 'Inactive';
    $data["PageNo"] = '';
    $data["Limit"] = '';

    $RetVal = $EmployeeRate->doSaveUploadEmployeeRates($data);

    return response()->json($RetVal);

  }

 //TEMP TABLE RATE
  public function getEmployeeTempRateList(Request $request){

    $EmployeeRate = new EmployeeRate();

    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["RateTempList"] = $EmployeeRate->getEmployeeTempRateList($param);

    //For Paging
    $RetVal['TotalRecord']=0;
    $RetVal['TotalRecord']=DB::table('payroll_employee_rates_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);

  }

  public function doSaveEmployeeTempRateBatch(Request $request){

  $EmployeeRate = new EmployeeRate();
  $ResponseMessage = "";

  $data['TempRateDataItems']=request("TempRateDataItems");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["EmployeeRateTempInfo"] = null;

  }else{

  $EmployeeRate->doSaveEmployeeTempRateBatch($data);

  $RetVal['Response'] = "Success";
  // $RetVal['ResponseMessage'] = "Employee rate is sucessfully uploaded.";
  }
  return response()->json($RetVal);

}

public function getEmployeeTempRateInfo(Request $request){

    $EmployeeRate = new EmployeeRate();

    $PlatformType=request("Platform");
    $EmployeeRateID = request("EmployeeRateID");

    $EmployeeTempRateInfo = $EmployeeRate->getEmployeeTempRateInfo($EmployeeRateID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeTempRateInfo"] = $EmployeeTempRateInfo;

    return response()->json($RetVal);
  }

  function doSaveEmployeeTempRate(Request $request){

    $Employee = new Employee();  
    $EmployeeRate = new EmployeeRate();

    $ResponseMessage = "";
    $data["EmployeeTempRateID"] =  request("EmployeeTempRateID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["EffectivityDate"] =  request("EffectivityDate");

    $data["MonthlyRate"] =  request("MonthlyRate");
    $data["DailyRate"] =  request("DailyRate");
    $data["HourlyRate"] =  request("HourlyRate");

    $data["RateRemarks"] =  request("RateRemarks");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;

      }else{

        $EmployeeRate_ID = $EmployeeRate->doSaveEmployeeTempRate($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee uploaded rate has saved successfully.";
                
      }
  
    return response()->json($RetVal);

  }

 public function doClearRateTempUpload(Request $request){

    //Clear Rate Temp
    $PlatformType=request("Platform");
    DB::table('payroll_employee_rates_temp')
             ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
             ->delete();
  }

  public function getTempRateUploadCount(){

    $MaxCount=0;
    $MaxCount=DB::table('payroll_employee_rates_temp')
                       ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
                       ->count();

   $RetVal['MaxCount']=$MaxCount;

    return response()->json($RetVal);
  }
 
 public function doRemoveDuplicateTempRateUpload(Request $request){

   $EmployeeRate = new EmployeeRate();
  $ResponseMessage = "";

  $tempID=request("tempID");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["RateTempList"] = null;

  }else{

  $EmployeeRate->doRemoveDuplicateTempRateUpload($tempID);

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "Employee with duplicate Rate entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

public function getEmployeeAndRateSearchList(Request $request){

    $EmployeeRate = new EmployeeRate();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Active';

    $EmployeeAndRateList = $EmployeeRate->getEmployeeRateList($param);

    $RetVal =array();
    foreach($EmployeeAndRateList as $row)
    { 

$data = $row->employee_id.'|'.   //Employee Details
        $row->employee_number.'|'.
        $row->first_name.'|'.
        $row->last_name.'|'.
        $row->middle_name.'|'.
        $row->FullName.'|'.
        $row->MobileNo.'|'.
        $row->EmployeeRateID.'|'. //Rates Details
        $row->MonthlyRate.'|'.
        $row->DailyRate.'|'.
        $row->HourlyRate.'|'.
        $row->EffectivityDateFormat;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function postEmployeeInfo(Request $request){

    $Employee = new Employee();

    $ResponseMessage = "";
    $data["id"] =  request('id');
    $data["parent_id"] = request('parent_id');

    $data["shortid"] = request('shortid');
    $data["first_name"] = request('first_name');
    $data["middle_name"] = request('middle_name');
    $data["last_name"] = request('last_name');
    $data["suffix"] = request('suffix');
    $data["nick_name"] = request('nick_name');

    $data["gender"] = request('gender');
    $data["birthdate"] = (empty(request('birthdate')) ?  null : request('birthdate'));
    $data["avatar"] = request('avatar');

    $data["present_address"] = request('present_address');
    $data["permanent_address"] = request('permanent_address');

    $data["nationality"] = request('nationality');
    $data["marital_status"] = request('marital_status');

    $data["contact_number"] = request('contact_number');
    $data["email"] = request('email');

    $data["username"] = request('username');
    $data["password"] = request('password');

    $data["status"] = request('status');

    $data["user_role"] = request('user_role');
    $data["note"] = request('note');

    $data["sss_number"] = request('sss_number');
    $data["pagibig_number"] = request('pagibig_number');
    $data["tin_number"] = request('tin_number');
    $data["philhealth_number"] = request('philhealth_number');
    $data["employee_number"] = request('employee_number');

    $data["company_branch_id"] = request('company_branch_id');
    $data["department_id"] = request('department_id');
    $data["job_title_id"] = request('job_title_id');
    $data["team_id"] = request('team_id');

    $data["date_entry"] = (empty(request('date_entry')) ?  null : request('date_entry'));
    $data["resignation_date"] = (empty(request('resignation_date')) ?  null : request('resignation_date'));
    $data["reset_token"] = (empty(request('reset_token')) ?  null : request('reset_token'));

    $data["created_by"] = (empty(request('created_by')) ?  null : request('created_by'));
    $data["updated_by"] = (empty(request('updated_by')) ?  null : request('updated_by'));

    $data["created_at"] = (empty(request('created_at')) ?  null : request('created_at'));
    $data["updated_at"] = (empty(request('updated_at')) ?  null : request('updated_at'));

    $data["employee_type"] = (empty(request('employee_type')) ?  null : request('employee_type'));
    $data["street"] = request('street');
    $data["barangay"] = request('barangay');
    $data["city"] = request('city');
    $data["province"] = request('province');
    $data["region"] = request('region');

    $data["approver_id"] = request('approver_id');
    $data["secretary_id"] = request('secretary_id');

    $data["old_employee_number"] = request('old_employee_number');

    $data["alternate_secretary"] = request('alternate_secretary');
    $data["hr_generalist_id"] = request('hr_generalist_id');
    $data["secondary_approver_id"] = request('secondary_approver_id');
    $data["salary_type"] = request('salary_type');
    
    if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{
        $RetVal['Response'] = $Employee->postEmployeeInfo($data);
        $RetVal['ResponseMessage'] = "Employee information has saved successfully.";
    }
  
    return response()->json($RetVal);

  }




}



