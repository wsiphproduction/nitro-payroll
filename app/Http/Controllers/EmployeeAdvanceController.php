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
use App\Models\PayrollPeriod;
use App\Models\EmployeeAdvance;

class EmployeeAdvanceController extends Controller {
 
  function SetAdminInitialData($data){

      $AdminUsers = new AdminUsers();  
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


public function showAdminAdvanceTransaction(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Advance';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-employee-advance-transaction')->with($data);

}

  public function getEmployeeAdvanceTransactionList(Request $request){

    $EmployeeAdvance = new EmployeeAdvance();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeAdvanceList"] = $EmployeeAdvance->getEmployeeAdvanceTransactionList($param);

    return response()->json($RetVal);

  }

  public function getEmployeeAdvanceTransactionInfo(Request $request){

    $EmployeeAdvance = new EmployeeAdvance();

    $PlatformType= request("Platform");
    $EmployeeAdvanceTransaction_ID = request("EmployeeAdvanceTransID");

    $EmployeeAdvanceTransactionInfo = $EmployeeAdvance->getEmployeeAdvanceTransactionInfo($EmployeeAdvanceTransaction_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeAdvanceTransactionInfo"] = $EmployeeAdvanceTransactionInfo;

    return response()->json($RetVal);
  }

  public function doSaveEmployeeAdvanceTransaction(Request $request){
       
    $EmployeeAdvance = new EmployeeAdvance();
 
    $ResponseMessage = "";
    $data["EmployeeAdvanceTransID"] =  request('EmployeeAdvanceTransID');
  
    $data["PayrollID"] =request("PayrollID");
    $data["PayrollCode"] =request("PayrollCode");
    
    $data["EmpID"] =request("EmpID");
    $data["EmpNo"] =request("EmpNo");
   
    $data["Year"] =request("Year");
    $data["TransDate"] =request("TransDate");

    $data["CutOffID"] =request("CutOff");

    $data["ReferenceNo"] =request("ReferenceNo");

    $data["DateIssued"] =request("DateIssued");
    $data["DateStartPayment"] =request("DateStartPayment");

    $data["AdvanceAmnt"] =request("AdvanceAmnt");
    $data["TotalAdvanceAmnt"] =request("TotalAdvanceAmnt");
    $data["AmortizationAmnt"] =request("AmortizationAmnt");
    $data["InterestAmnt"] =request("InterestAmnt");
    
    $data["TotalPayment"] =request("TotalPayment");
    $data["TotalBalance"] =request("TotalBalance");

    $data["Remarks"] =request("Remarks");
    $data["Status"] =request("Status");
    $data["IsUploaded"] =request("IsUploaded");

    // if($data["EmployeeAdvanceTransID"]<=0){
    //   if($EmployeeAdvance->doCheckExistingEmployeeAdvance($data)){
    //     $ResponseMessage= $data["EmployeeNo"]." Employee has existing active advance record.";
    //   }
    // }

      if($EmployeeAdvance->doCheckExistingEmployeeAdvance($data)){
        $ResponseMessage= "Selected employee has alread existing advance with the same category";
     }
      
    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeAdvanceTransactionInfo"] = null;

      }else{

        $EmployeeAdvanceTrans_ID = $EmployeeAdvance->doSaveEmployeeAdvanceTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] =  $data["EmpNo"]." Employee advance transaction has saved successfully.";
        $RetVal["EmployeeAdvanceTransactionInfo"] =  $EmployeeAdvance->getEmployeeAdvanceTransactionInfo($EmployeeAdvanceTrans_ID);

      }
  
    return response()->json($RetVal);
  }

 //TEMP TABLE
   public function getEmployeeAdvanceTempTransactionList(Request $request){

    $EmployeeAdvance = new EmployeeAdvance();

    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeAdvanceTempList"] = $EmployeeAdvance->getEmployeeAdvanceTransactionTempList($param);

    //For Paging
    $RetVal['TotalRecord']=0;
    $RetVal['TotalRecord']=DB::table('payroll_employee_advance_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);
  }

    public function getEmployeeAdvanceTempTransactionInfo(Request $request){

    $EmployeeAdvance = new EmployeeAdvance();

    $PlatformType= request("Platform");
    $EmployeeAdvanceTrans_ID = request("EmployeeAdvanceTransID");

    $EmployeeAdvanceTransactionInfo = $EmployeeAdvance->getEmployeeAdvanceTempTransactionInfo($EmployeeAdvanceTrans_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeAdvanceTransactionInfo"] = $EmployeeAdvanceTransactionInfo;

    return response()->json($RetVal);
  }

  public function doSaveAdvanceTempTransactionPerBatch(Request $request){

  $EmployeeAdvance = new EmployeeAdvance();
  $ResponseMessage = "";

  $data['AdvanceTempDataItems']=request("AdvanceTempDataItems");

  if(!empty($ResponseMessage)){

    $RetVal['Response'] = "Failed";
    $RetVal['ResponseMessage'] = $ResponseMessage;
  
  }else{

  $EmployeeAdvance->doSaveAdvanceTempTransactionPerBatch($data);

  $RetVal['Response'] = "Success";
  // $RetVal['ResponseMessage'] = "Employee with duplicate Income & Deduction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

  public function doSaveUploadFinalEmployeeAdvanceTransaction(){

    $EmployeeAdvance = new EmployeeAdvance();

    $data["SearchText"] = '';
    $data["Status"] = 'Pending';
    $data["PageNo"] = '';
    $data["Limit"] = '';

    $RetVal = $EmployeeAdvance->doSaveUploadFinalEmployeeAdvanceTransaction($data);

    return response()->json($RetVal);

  }
  
  public function doSaveEmployeeAdvanceTempTransaction(Request $request){
       
    $Employee = new Employee();   
    $PayrollPeriod= new PayrollPeriod();   
    $EmployeeAdvance = new EmployeeAdvance();
 
    $data["EmpID"]=0;
    $data["PayrollID"]=0;
    $ResponseMessage = "";

    $data["EmployeeAdvanceTransID"] =  request('EmployeeAdvanceTransID');

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

    $data["CutOffID"]=0;
    $data["CutOff"] =  request("CutOff");

    if(is_numeric($data["CutOff"])){
        $data["CutOffID"]=request("CutOff");
    }else{
       if($data["CutOff"]==config('app.PERIOD_1ST_HALF')){
        $data["CutOffID"]=1;
      }else if($data["CutOff"]==config('app.PERIOD_2ND_HALF')){
        $data["CutOffID"]=2;
      }else{
        $data["CutOffID"]=3;
      }
    }

    $data["ReferenceNo"] =  request("ReferenceNo");

    $data["DateIssued"] =  request("DateIssued");
    $data["DateStartPayment"] =  request("DateStartPayment");

    $data["AdvanceAmnt"] =  request("AdvanceAmnt");
    $data["TotalAdvanceAmnt"] =  request("TotalAdvanceAmnt");
    $data["AmortizationAmnt"] =  request("AmortizationAmnt");
    $data["InterestAmnt"] =  request("InterestAmnt");
    
    $data["TotalPayment"] =  request("TotalPayment");
    $data["TotalBalance"] =  request("TotalBalance");

    $data["Remarks"] =  request("Remarks");
    $data["Status"] =  request("Status");
    $data["IsUploaded"] =  request("IsUploaded");

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeAdvanceTempTransactionInfo"] = null;


      }else{

        $EmployeeAdvanceTrans_ID = $EmployeeAdvance->doSaveEmployeeAdvanceTempTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] =  "Employee income & deduction transaction has saved successfully.";
        $RetVal["EmployeeAdvanceTempTransactionInfo"] =  $EmployeeAdvance->getEmployeeAdvanceTempTransactionInfo($EmployeeAdvanceTrans_ID);

      }
  
    return response()->json($RetVal);

  }

public function doRemoveDuplicateAdvanceTempTransaction(Request $request){

 $EmployeeAdvance = new EmployeeAdvance();
  $ResponseMessage = "";

  $tempID=request("tempID");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["EmployeeAdvanceTempTransactionInfo"] = null;

  }else{

   $EmployeeAdvance->doRemoveDuplicateAdvanceTempTransaction($tempID);

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "Employee with duplicate advances transaction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}
  public function doClearAdvanceTempTransaction(Request $request){
        //clear temp table for reupload
        $PlatformType=request("Platform");
        DB::table('payroll_employee_advance_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();
  }

  public function getTempAdvanceTransactionCount(){

    $RetVal['MaxCount']=0;
    $RetVal['MaxCount']=DB::table('payroll_employee_advance_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);
  }
  

}



