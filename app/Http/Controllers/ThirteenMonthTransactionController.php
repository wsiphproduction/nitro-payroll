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
use App\Models\ThirteenMonthTransaction;
use App\Models\PayrollTransaction;

class ThirteenMonthTransactionController extends Controller {
 
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


public function showAdmin13thMonthTransaction(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = '13th Month Transaction';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-13thmonth-transaction')->with($data);

}

  public function get13thMonthTransactionList(Request $request){

    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $param["PayrollPeriodID"] = request("PayrollPeriodID");  
    $param["BranchID"] = request("BranchID");  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["ThirteenMonthTransactionList"] = $ThirteenMonthTransaction->get13thMonthTransactionList($param);

    return response()->json($RetVal);

  }

  public function getThirteenMonthTransactionSearchList(Request $request){

    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $param["PayrollPeriodID"] = 0;  
    $param["BranchID"] = 0;  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = '';
    $param["PageNo"] = 0;
    $param["Limit"] = config('app.ListRowLimit');
    $ThirteenMonthTransactionList = $ThirteenMonthTransaction->get13thMonthTransactionList($param);

    $RetVal =array();
    foreach($ThirteenMonthTransactionList as $row)
    { 

        $data = $row->ID.'|'.
        $row->TransNo.'|'.
        $row->TransDateTime.'|'.
        $row->BranchID.'|'.
        $row->BranchName.'|'.
        $row->PayrollPeriodID.'|'.
        $row->PayrollPeriodCode.'|'.
        $row->PayrollPeriodStartDate.'|'.
        $row->PayrollPeriodEndDate.'|'.
        $row->Remarks.'|'.
        $row->Status;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function get13thMonthTransactionInfo(Request $request){

    $ThirteenMonthTransaction = new ThirteenMonthTransaction();
    $PayrollTransaction = new PayrollTransaction();

    $PayrollTransactionID = request("PayrollTransactionID");
    $EmployeeID = request("EmployeeID");
    $Status = request("Status");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["ThirteenMonthTransactionInfo"] = $ThirteenMonthTransaction->get13thMonthTransactionInfo($PayrollTransactionID);

    $RetVal["ThirteenMonthTransactionEmployeeInfo"] = null;
    $RetVal["ThirteenMonthTransactionEmployeeBasicSalaryList"] = null;
    $RetVal["ThirteenMonthTransactionEmployeeLeaveList"] = null;
    $RetVal["ThirteenMonthTransactionEmployeeLateList"] = null;
    $RetVal["ThirteenMonthTransactionEmployeeUnderTimeList"] = null;
    if($EmployeeID > 0){
        $param["PayrollTransactionID"] = $PayrollTransactionID;
        $param["EmployeeID"] = $EmployeeID;
        $param["Status"] = $Status;
        $RetVal["ThirteenMonthTransactionEmployeeInfo"] = $ThirteenMonthTransaction->get13thMonthTransactionEmployeeInfo($param);

        $param["DateStart"] = date("Y-m-d");
        $param["DateEnd"] = date("Y-m-d");
        if(isset($RetVal["ThirteenMonthTransactionInfo"])){
          $param["DateStart"] = $RetVal["ThirteenMonthTransactionInfo"]->PayrollPeriodStartDate;
          $param["DateEnd"] = $RetVal["ThirteenMonthTransactionInfo"]->PayrollPeriodEndDate;
        }        

        $param["ReferenceType"] = "Basic Salary";
        $RetVal["ThirteenMonthTransactionEmployeeBasicSalaryList"] = $PayrollTransaction->getPayrollTransactionEmployeeListByReferenceType($param);
        
        $param["ReferenceType"] = "Leave";
        $RetVal["ThirteenMonthTransactionEmployeeLeaveList"] = $PayrollTransaction->getPayrollTransactionEmployeeListByReferenceType($param);
        
        $param["ReferenceType"] = "Late Hours";
        $RetVal["ThirteenMonthTransactionEmployeeLateList"] = $PayrollTransaction->getPayrollTransactionEmployeeListByReferenceType($param);

        $param["ReferenceType"] = "Undertime Hours";
        $RetVal["ThirteenMonthTransactionEmployeeUnderTimeList"] = $PayrollTransaction->getPayrollTransactionEmployeeListByReferenceType($param);

    }

    return response()->json($RetVal);
  }

  public function get13thMonthTransactionInfoTransNo(Request $request){

    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $TransNo = request("TransNo");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["ThirteenMonthTransactionInfo"] = $ThirteenMonthTransaction->get13thMonthTransactionInfoTransNo($TransNo);

    return response()->json($RetVal);
  }

  public function get13thMonthTransactionEmployeeList(Request $request){

    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $param["PayrollTransactionID"] = request("PayrollTransactionID");  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["ThirteenMonthTransactionEmployeeList"] = $ThirteenMonthTransaction->get13thMonthTransactionEmployeeList($param);

    return response()->json($RetVal);
  }

  public function doGenerate13thMonthTransaction(Request $request){
       
    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $ResponseMessage = "";
    $data["PayrollTransactionID"] =  request('PayrollTransactionID');
    $data["PayrollType"] =  request("PayrollType");
    $data["PayrollPeriodID"] =  request("PayrollPeriodID");
    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Remarks"] =  "";
    $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["JobTypeID"] = 0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
    }

    if($data["PayrollPeriodID"] == "" || $data["PayrollPeriodID"] == "0"){
        $ResponseMessage= "Please select payroll period.";
    }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_BATCH') && $data["FilterType"] == "Location" && ($data["BranchID"] == "0" || $data["BranchID"] == "")){
        $ResponseMessage= "Please select location.";
    }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_BATCH') && $data["FilterType"] == "Division" && ($data["DivisionID"] == "0" || $data["DivisionID"] == "")){
        $ResponseMessage= "Please select division.";
    }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_BATCH') && $data["FilterType"] == "Department" && ($data["DepartmentID"] == "0" || $data["DepartmentID"] == "")){
        $ResponseMessage= "Please select department.";
    }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_BATCH') && $data["FilterType"] == "Section" && ($data["SectionID"] == "0" || $data["SectionID"] == "")){
        $ResponseMessage= "Please select section.";
    }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_BATCH') && $data["FilterType"] == "Job Type" && ($data["JobTypeID"] == "0" || $data["JobTypeID"] == "")){
        $ResponseMessage= "Please select job type.";
    }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_EMPLOYEE') && ($data["EmployeeID"] == "0" || $data["EmployeeID"] == "")){
        $ResponseMessage= "Sorry. Unable to identify employee payroll.";
    }

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["ThirteenMonthTransactionInfo"] = null;
        $RetVal["ThirteenMonthTransactionEmployeeInfo"] = null;

    }else{

        if($data["PayrollType"] == config('app.GENERATE_13THMONTH_BATCH')){
          $PayrollTransactionID = $ThirteenMonthTransaction->doGenerate13thMonthTransaction($data);
          $RetVal['ResponseMessage'] = "13th month transaction has generated successfully.";
          $RetVal["ThirteenMonthTransactionEmployeeInfo"] = null;
        }else if($data["PayrollType"] == config('app.GENERATE_13THMONTH_EMPLOYEE')){
          $PayrollTransactionID = $ThirteenMonthTransaction->doRegenerate13thMonthTransaction($data);
          $RetVal['ResponseMessage'] = "Employee 13th month has been regenerated successfully.".$data["EmployeeID"];
          $RetVal["ThirteenMonthTransactionEmployeeInfo"] = $ThirteenMonthTransaction->get13thMonthTransactionEmployeeInfo($data);
        }

        $RetVal['Response'] = "Success";
        $RetVal["ThirteenMonthTransactionInfo"] =  $ThirteenMonthTransaction->get13thMonthTransactionInfo($PayrollTransactionID);

      }
  
    return response()->json($RetVal);

  }

  public function doApprove13thMonthTransaction(Request $request){
       
    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $ResponseMessage = "";
    $data["PayrollTransactionID"] =  request('PayrollTransactionID');

    if($data["PayrollTransactionID"] == "" || $data["PayrollTransactionID"] == "0"){
        $ResponseMessage= "Unable to identify 13th month transaction.";
    }

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["ThirteenMonthTransactionInfo"] = null;

    }else{

        $PayrollTransactionID = $ThirteenMonthTransaction->doApprove13thMonthTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "13th Month transaction has been approved successfully.";
        $RetVal["ThirteenMonthTransactionInfo"] =  $ThirteenMonthTransaction->get13thMonthTransactionInfo($PayrollTransactionID);

      }
  
    return response()->json($RetVal);

  }

  public function doCancel13thMonthTransaction(Request $request){
       
    $ThirteenMonthTransaction = new ThirteenMonthTransaction();

    $ResponseMessage = "";
    $data["PayrollTransactionID"] =  request('PayrollTransactionID');

    if($data["PayrollTransactionID"] == "" || $data["PayrollTransactionID"] == "0"){
        $ResponseMessage= "Unable to identify 13th month transaction.";
    }

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["ThirteenMonthTransactionInfo"] = null;

    }else{

        $PayrollTransactionID = $ThirteenMonthTransaction->doCancel13thMonthTransaction($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "13th Month transaction has been cancelled successfully.";
        $RetVal["ThirteenMonthTransactionInfo"] =  $ThirteenMonthTransaction->get13thMonthTransactionInfo($PayrollTransactionID);

      }
  
    return response()->json($RetVal);

  }

}



