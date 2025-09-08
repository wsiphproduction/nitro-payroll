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
use App\Models\LoanType;
use App\Models\AdminUsers;
use App\Models\EmployeeLoan;
use App\Models\PayrollPeriod;
use App\Models\Branch;
use App\Models\BranchSite;
use App\Models\PayrollSetting;

class EmployeeLoanController extends Controller {
 
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


public function showAdminLoanTransaction(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Loan';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

  return View::make('admin/admin-employee-loan-transaction')->with($data);

}

 public function getEmployeeLoanTransactionList(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanList"] = $EmployeeLoan->getEmployeeLoanTransactionList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $EmployeeLoanList = $EmployeeLoan->getEmployeeLoanTransactionList($param);
    $RetVal["TotalRecord"] = count($EmployeeLoanList);

    return response()->json($RetVal);

  }

  public function getEmployeeLoanTransactionInfo(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $PlatformType= request("Platform");
    $EmployeeLoanTrans_ID = request("EmployeeLoanTransID");

    $EmployeeLoanTransactionInfo = $EmployeeLoan->getEmployeeLoanTransactionInfo('ByID',$EmployeeLoanTrans_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanTransactionInfo"] = $EmployeeLoanTransactionInfo;

    return response()->json($RetVal);
  }

   public function doSetLoanTransactionStatus(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $ResponseMessage = "";
    $data["LoanID"] =  request('LoanID');
    $data["NewStatus"] =  request('NewStatus');

    $EmployeeLoan->doSetLoanTransactionStatus($data);

    if($data["NewStatus"]=='Approved'){
       $ResponseMessage = "Employee Loan has set to approved successfully.";
    }
     if($data["NewStatus"]=='Cancelled'){
        $ResponseMessage = "Employee Loan has set to cancelled successfully.";
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = $ResponseMessage;
    $RetVal["EmployeeLoanTransactionInfo"] =  $EmployeeLoan->getEmployeeLoanTransactionInfo('ByID',$data["LoanID"]);
    return response()->json($RetVal);
  }

   public function doSaveEmployeeLoanTransaction(Request $request){
       
    $EmployeeLoan = new EmployeeLoan();
 
    $ResponseMessage = "";
    $data["EmployeeLoanTransID"]=request("EmployeeLoanTransID");
    $data["EmpID"] =request("EmpID");
    $data["EmpNo"] =request("EmpNo");
   
    $data["LoanTypeID"] =request("LoanTypeID");
    $data["LoanTypeCode"] =request("LoanTypeCode");

    $data["TransDate"] =request("TransDate");
    $data["CutOffID"] =request("CutOff");

    $data["VoucherNo"] =request("VoucherNo");
    $data["DelayedOptions"] =request("DelayedOptions");

    $data["DateIssued"] =request("DateIssued");
    $data["DateStartPayment"] =request("DateStartPayment");

    $data["LoanAmnt"] =request("LoanAmnt");
    $data["TotalLoanAmnt"] =request("TotalLoanAmnt");
    $data["AmortizationAmnt"] =request("AmortizationAmnt");
    $data["InterestAmnt"] =request("InterestAmnt");
    $data["MonthsToPay"] =request("MonthsToPay");

    $data["Remarks"] =request("Remarks");
    $data["Status"] =request("Status");
    $data["IsUploaded"] =request("IsUploaded");

    if($EmployeeLoan->doCheckExistingEmployeeLoan($data)){
        $ResponseMessage= "Selected employee has already existing pending loan with same loan category of ".$data["LoanTypeCode"]."." ;
     }
      
      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeLoanTransactionInfo"] = null;

      }else{

        $EmployeeLoanTransID = $EmployeeLoan->doSaveEmployeeLoanTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] =  $data["EmpNo"]." Employee loan transaction has saved successfully.";
        $RetVal["EmployeeLoanTransactionInfo"] =  $EmployeeLoan->getEmployeeLoanTransactionInfo('ByID',$EmployeeLoanTransID);

      }
  
    return response()->json($RetVal);

  }

  function doSaveEmployeeLoanPayment(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $ResponseMessage = "";
    $data["LoanPaymentTransID"] =  request("LoanPaymentTransID");
    $data["EmployeeLoanTransID"] =  request("EmployeeLoanTransID");

    $data["EmployeeID"] =  request("EmployeeID");
    $data["PaymentDate"] =  request("PaymentDate");
    $data["PaymentLoanAmount"] =  request("PaymentLoanAmount");
    $data["PaymentRemarks"] =  request("PaymentRemarks");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeRateInfo"] = null;

      }else{

        $LoanPaymentTransID = $EmployeeLoan->doSaveEmployeeLoanPayment($data);
        $RetVal["EmployeeLoanTransactionInfo"] =  $EmployeeLoan->getEmployeeLoanTransactionInfo('ByID',$data["EmployeeLoanTransID"]);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee loan payment has saved successfully.";        
                      
      }
  
    return response()->json($RetVal);

  }

  public function getEmployeeLoanManualPaymeList(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $param["LoanTransID"] = request("LoanTransID");  
    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = $request["Status"];
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanManualPaymentList"] = $EmployeeLoan->getEmployeeLoanPaymentManualTransactionList($param);

    return response()->json($RetVal);

  }

  public function getEmployeeLoanLedgerPaymentList(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $param["LoanTransID"] = request("LoanTransID");  
    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = 0;
    $param["PageNo"] = 0;
    
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanLegderPaymentList"] = $EmployeeLoan->getEmployeeLoanPaymentLedgerList($param);

    return response()->json($RetVal);

  }

  public function getEmployeeLoanHistory(Request $request){
 
    $EmployeeLoan = new EmployeeLoan();

    $param["EmployeeID"] = request("EmployeeID");  
    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = $request["Status"];

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal['EmployeeLoanHistory']=$EmployeeLoan->getEmployeeLoanTransactionListByEmployeeID($param);

  return response()->json($RetVal);

  }

  public function showAdminEmployeeLoanPaymentHistoryPrintReport(Request $request){

    $Employee = new Employee();    
    $EmployeeLoan = new EmployeeLoan();
    $PayrollSetting = new PayrollSetting();
 
    $data["LoanTransactionID"] = request("LoanTransactionID");  
    $data["Page"]='Employee Loan Payment History Report';

    $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));    

    $info=$EmployeeLoan->getEmployeeLoanTransactionInfo('ByID',$data["LoanTransactionID"]);
    if(isset($info)>0){
       $data['LoanInformation']=$info;
       $data['EmployeeInformation']=$Employee->getEmployeeInfo('ByID',$info->EmployeeID);       
    }
    
    $data['SearchText']='';
    $data['Limit']=0;
    $data['PageNo']=0;

    $data['LoanTransID']=$data["LoanTransactionID"];    
    $data['LoanPaymentHistory']=$EmployeeLoan->getEmployeeLoanPaymentLedgerList($data);
            
    return View::make('admin/admin-employee-loan-payment-history-print-report')->with($data);
  
  }

 //TEMP TABLE
   public function getEmployeeLoanTempTransactionList(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanTempList"] = $EmployeeLoan->getEmployeeLoanTransactionTempList($param);

    //For Error Record
    $RetVal['TotalError']=0;
    $RetVal['TotalError']=DB::table('payroll_employee_loan_temp')->where('IsUploadError','=',1)->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    //For Paging
    $RetVal['TotalRecord']=0;
    $RetVal['TotalRecord']=DB::table('payroll_employee_loan_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);
  }

    public function getEmployeeLoanTempTransactionInfo(Request $request){

    $EmployeeLoan = new EmployeeLoan();

    $PlatformType= request("Platform");
    $EmployeeLoanTrans_ID = request("EmployeeLoanTransID");

    $EmployeeLoanTransactionInfo = $EmployeeLoan->getEmployeeLoanTempTransactionInfo($EmployeeLoanTrans_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanTransactionInfo"] = $EmployeeLoanTransactionInfo;

    return response()->json($RetVal);
  }

  public function doSaveLoanTempTransactionPerBatch(Request $request){

  $EmployeeLoan = new EmployeeLoan();
  $ResponseMessage = "";

  $data['LoanTempDataItems']=request("LoanTempDataItems");

  if(!empty($ResponseMessage)){

    $RetVal['Response'] = "Failed";
    $RetVal['ResponseMessage'] = $ResponseMessage;
  
  }else{

  $EmployeeLoan->doSaveLoanTempTransactionPerBatch($data);

  $RetVal['Response'] = "Success";
  // $RetVal['ResponseMessage'] = "Employee with duplicate Income & Deduction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

  public function doSaveEmployeeLoanTempTransactionPerBatch(Request $request){

  $EmployeeLoan = new EmployeeLoan();
  $ResponseMessage = "";

  $data['LoanTempDataItems']=request("LoanTempDataItems");

  if(!empty($ResponseMessage)){

    $RetVal['Response'] = "Failed";
    $RetVal['ResponseMessage'] = $ResponseMessage;
  
  }else{

  $EmployeeLoan->doSaveEmployeeLoanTempTransactionPerBatch($data);

  $RetVal['Response'] = "Success";
  // $RetVal['ResponseMessage'] = "Employee with duplicate Income & Deduction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

  public function doSaveUploadFinalEmployeeLoanTransaction(){

    $EmployeeLoan = new EmployeeLoan();

    $data["SearchText"] = '';
    $data["Status"] = 'Pending';
    $data["PageNo"] = '';
    $data["Limit"] = '';

    $RetVal = $EmployeeLoan->doSaveUploadFinalEmployeeLoanTransaction($data);

    return response()->json($RetVal);

  }
  
  public function doSaveEmployeeLoanTempTransaction(Request $request){
       
    $Employee = new Employee();  
    $LoanType = new LoanType(); 
    $PayrollPeriod= new PayrollPeriod();   
    $EmployeeLoan = new EmployeeLoan();

    $data["EmpID"]=0;
    $data["PayrollID"]=0;
    $data["LoanTypeID"]=0;
    $ResponseMessage = "";

    $data["EmployeeLoanTransID"] =  request('EmployeeLoanTransID');

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

    $data["LoanTypeCode"] =  request("LoanTypeCode");
    $loan_type_info = $LoanType->getLoanTypeInfoByCode($data["LoanTypeCode"]);
    if(isset($loan_type_info)>0){
       $data["LoanTypeID"]=$loan_type_info->ID;
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

    $data["VoucherNo"] =  request("VoucherNo");

    $data["DateIssued"] =  request("DateIssued");
    $data["DateStartPayment"] =  request("DateStartPayment");

    $data["LoanAmnt"] =  request("LoanAmnt");
    $data["TotalLoanAmnt"] =  request("TotalLoanAmnt");
    $data["AmortizationAmnt"] =  request("AmortizationAmnt");
    $data["InterestAmnt"] =  request("InterestAmnt");

    $data["MonthsToPay"] =  request("MonthsToPay");
    $data["TotalPayment"] =  request("TotalPayment");
    $data["TotalBalance"] =  request("TotalBalance");

    $data["Remarks"] =  request("Remarks");
    $data["Status"] =  request("Status");
    $data["IsUploaded"] =  request("IsUploaded");

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["EmployeeLoanTempTransactionInfo"] = null;


      }else{

        $EmployeeLoanTrans_ID = $EmployeeLoan->doSaveEmployeeLoanTempTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] =  "Employee loan transaction has saved successfully.";
        $RetVal["EmployeeLoanTempTransactionInfo"] =  $EmployeeLoan->getEmployeeLoanTempTransactionInfo($EmployeeLoanTrans_ID);

      }
  
    return response()->json($RetVal);

  }

public function doRemoveDuplicateLoanTempTransaction(Request $request){

  $EmployeeLoan = new EmployeeLoan();
  $ResponseMessage = "";

  $tempID=request("tempID");

  if(!empty($ResponseMessage)){

  $RetVal['Response'] = "Failed";
  $RetVal['ResponseMessage'] = $ResponseMessage;
  $RetVal["EmployeeLoanTempTransactionInfo"] = null;

  }else{

   $EmployeeLoan->doRemoveDuplicateLoanTempTransaction($tempID);

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "Employee with duplicate loan transaction entry was successfully deleted.";
  }
  return response()->json($RetVal);

}

  public function doClearLoanTempTransaction(Request $request){
        //clear temp table for reupload
        $PlatformType=request("Platform");
        DB::table('payroll_employee_loan_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();
  }

  public function getTempLoanTransactionCount(){

    $RetVal['MaxCount']=0;
    $RetVal['MaxCount']=DB::table('payroll_employee_loan_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->count();

    return response()->json($RetVal);
  }
  


}



