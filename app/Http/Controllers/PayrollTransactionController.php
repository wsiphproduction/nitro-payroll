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
use App\Models\Division;
use App\Models\Section;
use App\Models\JobType;
use App\Models\Employee;
use App\Models\Department;
use App\Models\AdminUsers;
use App\Models\PayrollTransaction;
use App\Models\PayrollPeriod;

class PayrollTransactionController extends Controller {
 
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


public function showAdminPayrollTransaction(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Transaction';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $Division = new Division();
  $data["DivisionList"] = $Division->getDivisionList($param);

  $Department = new Department();
  $data["DepartmentList"] = $Department->getDepartmentList($param);

  $Section = new Section();
  $data["SectionList"] = $Section->getSectionList($param);

  $JobType = new JobType();
  $data["JobTypeList"] = $JobType->getJobTypeList($param);

  $Employee = new Employee();
  $data["EmployeeList"] = $Employee->getEmployeeList($param);


  return View::make('admin/admin-payroll-transaction')->with($data);

}

  public function getPayrollTransactionList(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $param["PayrollPeriodID"] = request("PayrollPeriodID");  
    $param["BranchID"] = request("BranchID");  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollTransactionList"] = $PayrollTransaction->getPayrollTransactionList($param);

    return response()->json($RetVal);

  }

  public function getPayrollTransactionSearchList(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $param["PayrollPeriodID"] = 0;  
    $param["BranchID"] = 0;  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = '';
    $param["PageNo"] = 0;
    $param["Limit"] = config('app.ListRowLimit');
    $PayrollTransactionList = $PayrollTransaction->getPayrollTransactionList($param);

    $RetVal =array();
    foreach($PayrollTransactionList as $row)
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
        $row->PayrollType.'|'.
        $row->EmployeeID.'|'.
        $row->EmployeeNo.'|'.
        $row->FullName.'|'.
        $row->Remarks.'|'.
        $row->Status;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function getPayrollTransactionInfo(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $PayrollTransactionID = request("PayrollTransactionID");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollTransactionInfo"] = $PayrollTransaction->getPayrollTransactionInfo($PayrollTransactionID);

    return response()->json($RetVal);
  }

  public function getPayrollTransactionInfoTransNo(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $TransNo = request("TransNo");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollTransactionInfo"] = $PayrollTransaction->getPayrollTransactionInfoTransNo($TransNo);

    return response()->json($RetVal);
  }

  public function getPayrollTransactionEmployeeListByPeriod(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $param["PayrollPeriodID"] = request("PayrollPeriodID");  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $RetVal["PayrollTransactionEmployeeList"] = $PayrollTransaction->getPayrollTransactionEmployeeListByPeriod($param);

    //Get Total Count of Payroll Transaction For Paging
    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $PayrollTransactionEmployeeList = $PayrollTransaction->getPayrollTransactionEmployeeListByPeriodCount($param);
    $RetVal['TotalRecord']=count($PayrollTransactionEmployeeList);

    //Get Total Pending
    $param["Status"] = config('app.STATUS_PENDING');
    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $PayrollTransactionEmployeeListPending = $PayrollTransaction->getPayrollTransactionEmployeeListByPeriodCount($param);
    
    $RetVal["IsHasPendingTransactions"] = count($PayrollTransactionEmployeeListPending);

    return response()->json($RetVal);
  }

  public function getPayrollTransactionEmployeeList(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $param["PayrollTransactionID"] = request("PayrollTransactionID");  
    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollTransactionEmployeeList"] = $PayrollTransaction->getPayrollTransactionEmployeeList($param);

    //Get Total Count of Payroll Transaction For Paging
    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $PayrollTransactionEmployeeList = $PayrollTransaction->getPayrollTransactionEmployeeList($param);
    $RetVal['TotalRecord']=count($PayrollTransactionEmployeeList);

    return response()->json($RetVal);
  }

  public function getPayrollTransactionDetails(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $param["PayrollTransactionID"] = request("PayrollTransactionID");  
    $param["EmployeeID"] = request("EmployeeID");  
    $param["Status"] = request("Status");  

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollTransactionEmployeeInfo"] = $PayrollTransaction->getPayrollTransactionEmployeeInfo($param);
    $RetVal["PayrollTransactionDetails"] = $PayrollTransaction->getPayrollTransactionDetails($param);

    return response()->json($RetVal);
  }

  public function getPayrollTransDetails(Request $request){

    $PayrollTransaction = new PayrollTransaction();

    $param["EmployeeID"] = request("EmployeeID");  
    $param["PayrollPeriodID"] = request("PayrollPeriodID");  

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $PayrollTransactionEmployeeInfo = $PayrollTransaction->getPayrollTransactionEmployeeInfoByPeriod($param);

    if(isset($PayrollTransactionEmployeeInfo)){
      $param["PayrollTransactionID"] = $PayrollTransactionEmployeeInfo->PayrollTransactionID;  
      $param["Status"] = "Approved";  
      $PayrollTransactionDetails = $PayrollTransaction->getPayrollTransactionDetails($param);

      $RetVal['PayrollPeriodID'] = $PayrollTransactionEmployeeInfo->PayrollPeriodID;

      $RetVal['EmployeeID'] = $PayrollTransactionEmployeeInfo->EmployeeID;
      $RetVal['EmployeeNo'] = $PayrollTransactionEmployeeInfo->EmployeeNo;
      $RetVal['FirstName'] = $PayrollTransactionEmployeeInfo->FirstName;
      $RetVal['MiddleName'] = $PayrollTransactionEmployeeInfo->MiddleName;
      $RetVal['LastName'] = $PayrollTransactionEmployeeInfo->LastName;

      $RetVal['ContactNumber'] = $PayrollTransactionEmployeeInfo->ContactNumber;
      $RetVal['EmailAddress'] = $PayrollTransactionEmployeeInfo->EmailAddress;

      $RetVal['Division'] = $PayrollTransactionEmployeeInfo->Division;
      $RetVal['Department'] = $PayrollTransactionEmployeeInfo->Department;
      $RetVal['Section'] = $PayrollTransactionEmployeeInfo->Section;
      $RetVal['JobTitle'] = $PayrollTransactionEmployeeInfo->JobTitle;

      $RetVal['SalaryType'] = ($PayrollTransactionEmployeeInfo->SalaryType == 1 ? "Daily" : "Monthly");
      $RetVal['MonthlyRate'] = $PayrollTransactionEmployeeInfo->MonthlyRate;
      $RetVal['HourlyRate'] = $PayrollTransactionEmployeeInfo->HourlyRate;

      $RetVal['TotalBasicSalaryQty'] = 0;
      $RetVal['TotalBasicSalary'] = 0;

      $RetVal['TotalNDQty'] = 0;
      $RetVal['TotalND'] = 0;

      $RetVal['TotalLeave'] = 0;
      $arLeaveList = array();

      $RetVal['TotalOvertime'] = 0;
      $arOvertimeList = array();

      $RetVal['TotalAbsentHoursQty'] = 0;
      $RetVal['TotalAbsentHours'] = 0;

      $RetVal['TotalLateHours'] = 0;
      $RetVal['TotalLate'] = 0;

      $RetVal['TotalOtherTaxableIncome'] = 0;
      $RetVal['TotalOtherNonTaxableIncome'] = 0;
      $arIncomeList = array();

      $RetVal['TotalUndertimeQty'] = 0;
      $RetVal['TotalUndertime'] = 0;

      $RetVal['TotalSSSEEContribution'] = 0;
      $RetVal['TotalSSSERContribution'] = 0;

      $RetVal['TotalPHICEEContribution'] = 0;
      $RetVal['TotalPHICERContribution'] = 0;

      $RetVal['TotalHDMFEEContribution'] = 0;
      $RetVal['TotalHDMFERContribution'] = 0;

      $RetVal['TotalWTax'] = 0;
      
      $RetVal['TotalLoan'] = 0;
      $arLoanList = array();

      $RetVal['TotalOtherDeduction'] = 0;
      $arDeductionList = array();

      if(count($PayrollTransactionDetails) > 0){
          foreach($PayrollTransactionDetails as $paydet){

              if($paydet->ReferenceType == "Basic Salary"){
                  $RetVal['TotalBasicSalaryQty'] = $RetVal['TotalBasicSalaryQty'] + $paydet->Qty;
                  $RetVal['TotalBasicSalary'] = $RetVal['TotalBasicSalary'] + $paydet->Total;
                  
              }else if($paydet->ReferenceType == "Night Differential"){
                  $RetVal['TotalNDQty'] = $RetVal['TotalNDQty'] + $paydet->Qty;
                  $RetVal['TotalND'] = $RetVal['TotalND'] + $paydet->Total;

              }else if($paydet->ReferenceType == "Leave"){
                  $RetVal['TotalLeave'] = $RetVal['TotalLeave'] + $paydet->Total;

                  $dtaLeave["ID"] = $paydet->ID;
                  $dtaLeave["Reference"] = $paydet->Reference;
                  $dtaLeave["Hours"] = $paydet->Qty;
                  $dtaLeave["Amount"] = $paydet->Total;
                  array_push($arLeaveList, $dtaLeave);

              }else if($paydet->ReferenceType == "Overtime"){
                  $RetVal['TotalOvertime'] = $RetVal['TotalOvertime'] + $paydet->Total;

                  $dtaOT["ID"] = $paydet->ID;
                  $dtaOT["Reference"] = $paydet->Reference;
                  $dtaOT["Hours"] = $paydet->Qty;
                  $dtaOT["Amount"] = $paydet->Total;
                  array_push($arOvertimeList, $dtaOT);

              }else if($paydet->ReferenceType == "Income"){

                  if($paydet->IsTaxable == 1){
                      $RetVal['TotalOtherTaxableIncome'] = $RetVal['TotalOtherTaxableIncome'] + $paydet->Total;
                  }else{
                      $RetVal['TotalOtherNonTaxableIncome'] = $RetVal['TotalOtherNonTaxableIncome'] + $paydet->Total;
                  }

                  $dtaIncome["ID"] = $paydet->ID;
                  $dtaIncome["Reference"] = $paydet->Reference;
                  $dtaIncome["IsTaxable"] = $paydet->IsTaxable;
                  $dtaIncome["Amount"] = $paydet->Total;
                  array_push($arIncomeList, $dtaIncome);

              }else if($paydet->ReferenceType == "Absent"){
                  $RetVal['TotalAbsentHoursQty'] = $RetVal['TotalAbsentHoursQty'] + $paydet->Qty;
                  $RetVal['TotalAbsentHours'] = $RetVal['TotalAbsentHours'] + $paydet->Total;

              }else if($paydet->ReferenceType == "Late Hours"){
                  $RetVal['TotalLateHours'] = $RetVal['TotalLateHours'] + $paydet->Qty;
                  $RetVal['TotalLate'] = $RetVal['TotalLate'] + $paydet->Total;

              }else if($paydet->ReferenceType == "Undertime Hours"){
                  $RetVal['TotalUndertimeQty'] = $RetVal['TotalUndertimeQty'] + $paydet->Qty;
                  $RetVal['TotalUndertime'] = $RetVal['TotalUndertime'] + $paydet->Total;

              }else if($paydet->ReferenceType == "SSS EE Contribution"){
                  $RetVal['TotalSSSEEContribution'] = $RetVal['TotalSSSEEContribution'] + $paydet->Total;

              }else if($paydet->ReferenceType == "SSS ER Contribution"){
                  $RetVal['TotalSSSERContribution'] = $RetVal['TotalSSSERContribution'] + $paydet->Total;

              }else if($paydet->ReferenceType == "PHIC EE Contribution"){
                  $RetVal['TotalPHICEEContribution'] = $RetVal['TotalPHICEEContribution'] + $paydet->Total;

              }else if($paydet->ReferenceType == "PHIC ER Contribution"){
                  $RetVal['TotalPHICERContribution'] = $RetVal['TotalPHICERContribution'] + $paydet->Total;

              }else if($paydet->ReferenceType == "HDMF EE Contribution"){
                  $RetVal['TotalHDMFEEContribution'] = $RetVal['TotalHDMFEEContribution'] + $paydet->Total;

              }else if($paydet->ReferenceType == "HDMF ER Contribution"){
                  $RetVal['TotalHDMFERContribution'] = $RetVal['TotalHDMFERContribution'] + $paydet->Total;

              }else if($paydet->ReferenceType == "Withholding Tax"){
                  $RetVal['TotalWTax'] = $RetVal['TotalWTax'] + $paydet->Total;


              }else if($paydet->ReferenceType == "Loan"){
                  $RetVal['TotalLoan'] = $RetVal['TotalLoan'] + $paydet->Total;

                  $dtaLoan["ID"] = $paydet->ID;
                  $dtaLoan["Reference"] = $paydet->Reference;
                  $dtaLoan["Amount"] = $paydet->Total;
                  array_push($arLoanList, $dtaLoan);

              }else if($paydet->ReferenceType == "Deduction"){
                  $RetVal['TotalOtherDeduction'] = $RetVal['TotalOtherDeduction'] + $paydet->Total;

                  $dtaDeduction["ID"] = $paydet->ID;
                  $dtaDeduction["Reference"] = $paydet->Reference;
                  $dtaDeduction["Amount"] = $paydet->Total;
                  array_push($arDeductionList, $dtaDeduction);

              }
          }
      }

      $RetVal['OvertimeList'] = $arOvertimeList;
      $RetVal['LeaveList'] = $arLeaveList;
      $RetVal['IncomeList'] = $arIncomeList;
      $RetVal['LoanList'] = $arLoanList;
      $RetVal['DeductionList'] = $arDeductionList;

    }

    return response()->json($RetVal);
  }

  public function doGeneratePayroll(Request $request){
       
    $PayrollTransaction = new PayrollTransaction();

    $ResponseMessage = "";
    $data["PayrollType"] =  request("PayrollType");
    $data["PayrollTransactionID"] = request("PayrollTransactionID");
    $data["PayrollPeriodID"] =  request("PayrollPeriodID");
    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");
    $data["ProcessNo"] =  request("ProcessNo");

    if($data["FilterType"] == "Location"){
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
    }else{
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }

    if($data["PayrollType"] == ""){
        $ResponseMessage= "Please select payroll type.";
    }else if($data["PayrollPeriodID"] == "" || $data["PayrollPeriodID"] == "0"){
        $ResponseMessage= "Please select payroll period.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH') && $data["FilterType"] == "Location" && ($data["BranchID"] == "0" || $data["BranchID"] == "")){
        $ResponseMessage= "Please select location.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH') && $data["FilterType"] == "Division" && ($data["DivisionID"] == "0" || $data["DivisionID"] == "")){
        $ResponseMessage= "Please select division.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH') && $data["FilterType"] == "Department" && ($data["DepartmentID"] == "0" || $data["DepartmentID"] == "")){
        $ResponseMessage= "Please select department.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH') && $data["FilterType"] == "Section" && ($data["SectionID"] == "0" || $data["SectionID"] == "")){
        $ResponseMessage= "Please select section.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH') && $data["FilterType"] == "Job Type" && ($data["JobTypeID"] == "0" || $data["JobTypeID"] == "")){
        $ResponseMessage= "Please select job type.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH') && $data["FilterType"] == "Employee" && ($data["EmployeeID"] == "0" || $data["EmployeeID"] == "")){
        $ResponseMessage= "Please select employee.";
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_FINAL') && ($data["EmployeeID"] == "0" || $data["EmployeeID"] == "")){
    }else if($data["PayrollType"] == config('app.GENERATE_PAYROLL_EMPLOYEE') && ($data["EmployeeID"] == "0" || $data["EmployeeID"] == "")){
        $ResponseMessage= "Sorry. Unable to identify employee payroll.";
    }

    $RetVal["PayrollTransactionInfo"] = null;
    $RetVal["PayrollTransactionEmployeeInfo"] = null;

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["PayrollTransactionInfo"] = null;
        $RetVal["PayrollTransactionEmployeeInfo"] = null;
        
    }else{

        if($data["PayrollType"] == config('app.GENERATE_PAYROLL_BATCH')){
          $data["PayrollTransactionID"] = $PayrollTransaction->doGeneratePayroll($data);

          $RetVal['ResponseMessage'] = "Payroll transaction has been generated successfully.";

          if($data["EmployeeID"] != "0" && $data["EmployeeID"] != ""){
            $RetVal["PayrollTransactionEmployeeInfo"] = $PayrollTransaction->getPayrollTransactionEmployeeInfo($data);
          }
        }

        $RetVal['Response'] = "Success";
        $RetVal["PayrollTransactionInfo"] =  $PayrollTransaction->getPayrollTransactionInfo($data["PayrollTransactionID"]);

    }
  
    return response()->json($RetVal);

  }

  public function doApproveGeneratedPayroll(Request $request){
       
    $PayrollTransaction = new PayrollTransaction();

    $ResponseMessage = "";
    $data["PayrollPeriodID"] =  request('PayrollPeriodID');

    if($data["PayrollPeriodID"] == "" || $data["PayrollPeriodID"] == "0"){
        $ResponseMessage= "Unable to identify payroll period.";
    }

    $data["PayrollTransactionID"] = 0;
    $PayrollTransactionInfo = $PayrollTransaction->getPayrollTransactionInfoByPeriod($data["PayrollPeriodID"], config('app.STATUS_PENDING'));
    if(isset($PayrollTransactionInfo)){
        $data["PayrollTransactionID"] = $PayrollTransactionInfo->ID;
    }

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["PayrollTransactionInfo"] = null;

    }else{

        $PayrollTransactionID = $PayrollTransaction->doApproveGeneratedPayroll($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Generated payroll has approved successfully.";
        $RetVal["PayrollTransactionInfo"] =  $PayrollTransaction->getPayrollTransactionInfo($PayrollTransactionID);

      }
  
    return response()->json($RetVal);

  }

  public function doCancelGeneratedPayroll(Request $request){
       
    $PayrollTransaction = new PayrollTransaction();

    $ResponseMessage = "";
    $data["PayrollPeriodID"] =  request('PayrollPeriodID');

    if($data["PayrollPeriodID"] == "" || $data["PayrollPeriodID"] == "0"){
        $ResponseMessage= "Unable to identify payroll period.";
    }

    if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["PayrollTransactionInfo"] = null;

    }else{

        $PayrollTransactionID = $PayrollTransaction->doCancelGeneratedPayroll($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Generated payroll has cancelled successfully.";
        $RetVal["PayrollTransactionInfo"] =  $PayrollTransaction->getPayrollTransactionInfo($PayrollTransactionID);

      }
  
    return response()->json($RetVal);

  }

  public function showAdminPayrollPaySlip(Request $request){

    $data['Page'] = 'Payroll Pay Slip';
    $data = $this->SetAdminInitialData($data);

    return View::make('admin/admin-payroll-payslip')->with($data);

  }

  public function getEmployeePayrollEarningHistory(Request $request){

    $PayrollTransaction = new PayrollTransaction();
 
    $param["EmployeeID"] = request("EmployeeID");  
    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = $request["Status"];

    $EmployeeLoan = new EmployeeLoan();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal['EmployeeLoanHistory']=$EmployeeLoan->getEmployeeLoanTransactionListByEmployeeID($param);

  return response()->json($RetVal);

  }

}



