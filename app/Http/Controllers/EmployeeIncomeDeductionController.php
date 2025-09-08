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
use App\Models\IncomeDeductionType;
use App\Models\EmployeeIncomeDeduction;
use App\Models\Branch;
use App\Models\BranchSite;
use App\Models\PayrollSetting;

class EmployeeIncomeDeductionController extends Controller {
 
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


public function showAdminIncomeDeductionTransaction(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Income/Deduction Transaction';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);
  
  return View::make('admin/admin-employee-income-deduction-transaction')->with($data);

}

  public function getEmployeeIncomeDeductionTransactionList(Request $request){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeIncomeDeductionList"] = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $EmployeeIncomeDeductionList = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionList($param);
    $RetVal["TotalRecord"] = count($EmployeeIncomeDeductionList);

    return response()->json($RetVal);

  }

  public function doSetEmployeeIncomeDeductionTransactionStatus(Request $request){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $ResponseMessage = "";
    $data["IncomeDeductionID"] =  request('IncomeDeductionID');
    $data["NewStatus"] =  request('NewStatus');

    $EmployeeIncomeDeduction->doSetEmployeeIncomeDeductionTransactionStatus($data);

    if($data["NewStatus"]=='Approved'){
       $ResponseMessage = "Employee Income Deduction has set to Approved successfully.";
    }

    if($data["NewStatus"]=='Cancelled'){
        $ResponseMessage = "Employee Income Deduction has set to Cancelled successfully.";
    }

    if($data["NewStatus"]=='OnHold'){
        $ResponseMessage = "Employee Income Payment Deduction has set to On Hold successfully.";
    }

    // if($data["NewStatus"]=='Resume'){
    //     $ResponseMessage = "Employee Income Payment Deduction has set to Resume successfully.";
    // }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = $ResponseMessage;
    $RetVal["EmployeeIncomeDeductionTransactionInfo"] =  $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionInfo('ByID',$data["IncomeDeductionID"]);
    return response()->json($RetVal);
  }

  public function getEmployeeIncomeDeductionTransactionInfo(Request $request){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $PlatformType= request("Platform");
    $EmployeeIncomeDeductionTrans_ID = request("EmployeeIncomeDeductionTransID");

    $EmployeeIncomeDeductionTransactionInfo = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionInfo('ByID',$EmployeeIncomeDeductionTrans_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeIncomeDeductionTransactionInfo"] = $EmployeeIncomeDeductionTransactionInfo;

    return response()->json($RetVal);
  }

   public function doSaveEmployeeIncomeDeductionTransaction(Request $request){
       
    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();
 
    $ResponseMessage = "";

    $data["EmployeeIncomeDeductionTransID"] =request('EmployeeIncomeDeductionTransID');
    
    $data["EmpID"] =request("EmpID");
    $data["EmpNo"] =request("EmpNo");
   
    $data["IncomeDeductionTypeID"] =request("IncomeDeductionTypeID");
    $data["IncomeDeductionTypeCode"] =request("IncomeDeductionTypeCode");
    $data["IncomeDeductionType"] =request("IncomeDeductionType");
    
    $data["TransDate"] =request("TransDate");

    $data["ReferenceNo"] =request("ReferenceNo");
    $data["DateIssued"] =request("DateIssued");    
    $data["DateStartPayment"] =request("DateStartPayment");    

    $data["ReleaseTypeID"]=request("ReleaseTypeID");
   
    $data["InterestAmnt"] =request("InterestAmnt");
    $data["AmortizationAmnt"] =request("AmortizationAmnt");
    $data["IncomeDeductionAmnt"] =request("IncomeDeductionAmnt");
    $data["TotalMonthsToPay"] =request("TotalMonthsToPay");
    $data["TotalIncomeDeductionAmnt"] =request("TotalIncomeDeductionAmnt");
        
    $data["Remarks"] =request("Remarks");
    $data["Status"] =request("Status");
    $data["IsUploaded"] =request("IsUploaded");

    if($EmployeeIncomeDeduction->doCheckExistingEmployeeIncomeDeduction($data)){

      if($data["IncomeDeductionType"]=="EARNING"){
          $ResponseMessage= "Selected employee has already existing pending Income with same category of ".$data["IncomeDeductionTypeCode"].".";
      }else{
         $ResponseMessage= "Selected employee has already existing pending Deduction with same category of ".$data["IncomeDeductionTypeCode"].".";
        }              
      }
     
      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeIncomeDeductionTransactionInfo"] = null;

      }else{

        $EmployeeIncomeDeductionTrans_ID = $EmployeeIncomeDeduction->doSaveEmployeeIncomeDeductionTransaction($data);

        $RetVal['Response'] = "Success";
        if($data["IncomeDeductionType"]=="EARNING"){
          $RetVal['ResponseMessage'] =  $data["EmpNo"]." Employee income transaction has saved successfully.";
      }else{
         $RetVal['ResponseMessage'] =  $data["EmpNo"]." Employee deduction transaction has saved successfully.";
                
      }
      $RetVal["EmployeeIncomeDeductionTransactionInfo"] = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionInfo('ByID',$EmployeeIncomeDeductionTrans_ID);

      }
  
    return response()->json($RetVal);

  }

  public function getEmployeeIncomeDeductionLedgerPaymentList(Request $request){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $param["IncomeDeductionTransID"] =$request["IncomeDeductionTransID"];  
    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = 0;
    $param["PageNo"] = 0;
    
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeIncomeDeductionHistory"] = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionPaymentLedgerList($param);

    return response()->json($RetVal);

  }

  public function showAdminEmployeeIncomeDeductionPaymentHistoryPrintReport(Request $request){

    $Employee = new Employee();        
    $PayrollSetting = new PayrollSetting();
    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();
 
    $data["IncomeDeductionTransactionID"] = request("IncomeDeductionTransactionID");  
    $data["Page"]='Employee Income Deduction Payment History Report';

    $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));    

    $info=$EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionInfo('ByID',$data["IncomeDeductionTransactionID"]);
    if(isset($info)>0){
       $data['IncomeDeductionInformation']=$info;
       $data['EmployeeInformation']=$Employee->getEmployeeInfo('ByID',$info->EmployeeID);       
    }
    
    $data['SearchText']='';
    $data['Limit']=0;
    $data['PageNo']=0;

    $data['IncomeDeductionTransID']=$data["IncomeDeductionTransactionID"];    
    $data['IncomeDeductionPaymentHistory']=$EmployeeIncomeDeduction->getEmployeeIncomeDeductionPaymentLedgerList($data);
            
    return View::make('admin/admin-employee-income-deduction-payment-history-print-report')->with($data);
  
  }
 
 //TEMP TABLE
   public function getEmployeeIncomeDeductionTempTransactionList(Request $request){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeIncomeDeductionTempList"] = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionTempList($param);

    //For Error Record
    $RetVal['TotalError']=0;
    $RetVal['TotalError']=DB::table('payroll_employee_income_deduction_temp')->where('IsUploadError','=',1)->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    //For Paging
    $RetVal['TotalRecord']=0;
    $RetVal['TotalRecord']=DB::table('payroll_employee_income_deduction_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);
  }

    public function getEmployeeIncomeDeductionTempTransactionInfo(Request $request){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $PlatformType= request("Platform");
    $EmployeeIncomeDeductionTrans_ID = request("EmployeeIncomeDeductionTransID");

    $EmployeeIncomeDeductionTransactionInfo = $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTempTransactionInfo($EmployeeIncomeDeductionTrans_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeIncomeDeductionTransactionInfo"] = $EmployeeIncomeDeductionTransactionInfo;

    return response()->json($RetVal);
  }

  public function doSaveIncomeDeductionTempTransactionPerBatch(Request $request){

  $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();
  $ResponseMessage = "";

  $data['IncomeDeductionTempDataItems']=request("IncomeDeductionTempDataItems");

  if(!empty($ResponseMessage)){

    $RetVal['Response'] = "Failed";
    $RetVal['ResponseMessage'] = $ResponseMessage;
  
  }else{

  $EmployeeIncomeDeduction->doSaveIncomeDeductionTempTransactionPerBatch($data);

  $RetVal['Response'] = "Success";
  // $RetVal['ResponseMessage'] = "Employee with duplicate Income & Deduction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

  public function doSaveUploadFinalEmployeeIncomeDeductionTransaction(){

    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();

    $data["SearchText"] = '';
    $data["Status"] = 'Pending';
    $data["PageNo"] = '';
    $data["Limit"] = '';

    $RetVal = $EmployeeIncomeDeduction->doSaveUploadFinalEmployeeIncomeDeductionTransaction($data);

    return response()->json($RetVal);

  }
  
  public function doSaveEmployeeIncomeDeductionTempTransaction(Request $request){
       
    $Employee = new Employee();   
    $PayrollPeriod= new PayrollPeriod();   
    $IncomeDeductionType= new IncomeDeductionType();
    $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();
 
    $data["EmpID"]=0;
    $data["ReleaseTypeID"]=0;
    $data["IncomeDeductionTypeID"]=0;
    $ResponseMessage = "";

    $data["EmployeeIncomeDeductionTransID"] =  request('EmployeeIncomeDeductionTransID');
    $data["EmpNo"] = request("EmpNo");

    $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$data["EmpNo"]);
    if(isset($emp_info)>0){
       $data["EmpID"]=$emp_info->employee_id;
    }  

    $data["IncomeDeductionTypeCode"] =  request("IncomeDeductionTypeCode");
    $income_deductoin_info = $IncomeDeductionType->getIncomeDeductionTypeInfoByCode($data["IncomeDeductionTypeCode"]);
    if(isset($income_deductoin_info)>0){
       $data["IncomeDeductionTypeID"]=$income_deductoin_info->ID; 
    }  
        
    $data["TransDate"] =  request("TransDate");
   
    $data["ReleaseTypeID"] =  request("ReleaseTypeID");    
    $data["ReferenceNo"] =  request("ReferenceNo");

    $data["DateIssued"] =  request("DateIssued");    
    $data["DateStartPayment"] =  request("DateStartPayment");  

    $data["InterestAmnt"] =  request("InterestAmnt");    

    $data["AmortizationAmnt"] =  request("AmortizationAmnt");    
    $data["IncomeDeductionAmnt"] =  request("IncomeDeductionAmnt");    
    $data["TotalIncomeDeductionAmnt"] =  request("TotalIncomeDeductionAmnt");    

    $data["Remarks"] =  request("Remarks");
    $data["Status"] =  request("Status");
    $data["IsUploaded"] =  request("IsUploaded");

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeIncomeDeductionTempTransactionInfo"] = null;

      }else{

        $EmployeeIncomeDeductionTrans_ID = $EmployeeIncomeDeduction->doSaveEmployeeIncomeDeductionTempTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] =  "Employee income & deduction transaction has saved successfully.";
        $RetVal["EmployeeIncomeDeductionTempTransactionInfo"] =  $EmployeeIncomeDeduction->getEmployeeIncomeDeductionTempTransactionInfo($EmployeeIncomeDeductionTrans_ID);

      }
  
    return response()->json($RetVal);

  }

public function doRemoveDuplicateIncomeDeductionTempTransaction(Request $request){

  $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();
  $ResponseMessage = "";

  $tempID=request("tempID");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["EmployeeIncomeDeductionTempTransactionInfo"] = null;

  }else{

   $EmployeeIncomeDeduction->doRemoveDuplicateIncomeDeductionTempTransaction($tempID);

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "Employee with duplicate income & deduction transaction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}
  public function doClearIncomeDeductionTempTransaction(Request $request){
    
        //clear temp table 
      $PlatformType=request("Platform");
      DB::table('payroll_employee_income_deduction_temp')
                 ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
                 ->delete();
  }

  public function getTempIncomeDeductionTransactionCount(){

    $RetVal['MaxCount']=0;
    $RetVal['MaxCount']=DB::table('payroll_employee_income_deduction_temp')
                        ->where('UploadedByID',Session::get('ADMIN_USER_ID'))
                        ->count();

    return response()->json($RetVal);
  }
  

}



