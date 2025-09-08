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
use App\Models\BranchSite;
use App\Models\Reports;
use App\Models\Section;
use App\Models\JobType;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Department;
use App\Models\AdminUsers;
use App\Models\EmployeeDTR;
use App\Models\EmployeeLoan;
use App\Models\PayrollPeriod;
use App\Models\PayrollSetting;
use App\Models\EmployeeAdvance;
use App\Models\PayrollTransaction;
use App\Models\EmployeeIncomeDeduction;
use App\Models\OTRate;
use App\Models\LeaveType;
use App\Models\IncomeDeductionType;
use App\Models\LoanType;

class ReportController extends Controller {
 
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

//EMPLOYEE LOAN LEDGER PRINT REPORT
public function showAdminEmployeeLoanLedgerReport(Request $request){


  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Loan Ledger Print Report';
  $data = $this->SetAdminInitialData($data);

  $data['ReferenceID']=request("ReferenceID");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $EmployeeLoan = new EmployeeLoan();
  $data['EmployeeLoanHeaderInfo']=$EmployeeLoan->getEmployeeLoanTransactionInfo('ByID',$data['ReferenceID']);

  $Reports = new Reports();
  $data['EmployeeLoanDetailsInfo']=$Reports->getEmployeeLoanDetailsInformationReport($data['ReferenceID']);

  return View::make('admin/admin-employee-loan-ledger-print-report')->with($data);

}

//EMPLOYEE ADVANCE PRINT REPORT
public function showAdminEmployeeAdvanceReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Advance Ledger Print Report';
  $data = $this->SetAdminInitialData($data);

  $data['ReferenceID']=request("ReferenceID");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));


  $EmployeeAdvance = new EmployeeAdvance();
  $data['EmployeeAdvanceHeaderInfo']=$EmployeeAdvance->getEmployeeAdvanceTransactionInfo($data['ReferenceID']);

  $Reports = new Reports();
  $data['EmployeeAdvanceDetailsInfo']=$Reports->getEmployeeAdvanceDetailsInformationReport($data['ReferenceID']);

  return View::make('admin/admin-employee-advance-ledger-print-report')->with($data);

}

// EMPLOYEE INCOME DEDUCTION PRINT REPORT
public function showAdminEmployeeIncomeDeductionLedgerReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Income Deduction Ledger Print Report';
  $data = $this->SetAdminInitialData($data);

  $data['ReferenceID']=request("ReferenceID");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));


  $EmployeeIncomeDeduction = new EmployeeIncomeDeduction();
  $data['EmployeeIncomeDeductionHeaderInfo']=$EmployeeIncomeDeduction->getEmployeeIncomeDeductionTransactionInfo($data['ReferenceID']);

  $IncomeDeductionCategory='';
  if(isset($data['EmployeeIncomeDeductionHeaderInfo'])){
    $IncomeDeductionCategory=$data['EmployeeIncomeDeductionHeaderInfo']->Category;
  }

  $Reports = new Reports();
  if($IncomeDeductionCategory=='EARNING'){
    $data['EmployeeIncomeDeductionDetailsInfo']=$Reports->getEmployeeIncomeDetailsInformationReport($data['ReferenceID']);
  }else{
     $data['EmployeeIncomeDeductionDetailsInfo']=$Reports->getEmployeeDeductionDetailsInformationReport($data['ReferenceID']);
  }

  return View::make('admin/admin-employee-income-deduction-ledger-print-report')->with($data);

}


//SSS CONTRIBUTION SHOW VIEW REPORT
public function showAdminSSSContributionReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'SSS Contribution Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

  return View::make('admin/admin-sss-contribution-report')->with($data);

}

// SSS CONTRIBUTION GET DATA LIST REPORT
public function getSSSEmployeeContributionList(Request $request){

    $Reports = new Reports();

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    $param["Status"] = request("Status");
    $param["Filter"] = request("Filter");
    $param["SearchText"] = request("SearchText");

    $param["Year"] = request("Year");
    $param["Month"] = request("Month");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    
    if($param["Status"]=='Approved'){
       $RetVal["SSSEmployeeContributionList"] = $Reports->getSSSApprovedEmployeeContribution($param);
    
      $param["PageNo"] = 0;
      $param["Limit"] = 0;
      $SSSEmployeeContributionList = $Reports->getSSSApprovedEmployeeContribution($param);
      $RetVal["TotalRecord"] = count($SSSEmployeeContributionList); 

    }else{

      $RetVal["SSSEmployeeContributionList"] = $Reports->getSSSPendingEmployeeContribution($param);
    
      $param["PageNo"] = 0;
      $param["Limit"] = 0;
      $SSSEmployeeContributionList = $Reports->getSSSPendingEmployeeContribution($param);
      $RetVal["TotalRecord"] = count($SSSEmployeeContributionList); 

    }
    

    return response()->json($RetVal);
}

// SSS CONTRIBUTION PRINT REPORT
public function showAdminSSSContributionPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }
 
  $Reports = new Reports();
  $data['Page'] = 'SSS Contribution Print Report';
  $data = $this->SetAdminInitialData($data);

  $data['Year']=request("Year");  
  $data['Month']=request("Month");  

  $data["PageNo"] = request("PageNo");
  $data["Limit"] = request("Limit");
  $data["Status"] = request("Status");
  $data["Filter"] = request("Filter");
  $data["SearchText"] = request("SearchText");

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  if($data['Status']=='Approved'){
      $data['Limit']=0;  
      $data['PageNo']=0;  
      $data["SSSEmployeeContributionList"] = $Reports->getSSSApprovedEmployeeContribution($data);
  }else{

    $data['Limit']=0;  
    $data['PageNo']=0;  
    $data["SSSEmployeeContributionList"] = $Reports->getSSSPendingEmployeeContribution($data);

  }
  
  return View::make('admin/admin-sss-contribution-print-report')->with($data);

}

//HDMF CONTRIBUTION VIEW REPORT
public function showAdminHDMFContributionReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'HDMF Contribution Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

  return View::make('admin/admin-hdmf-contribution-report')->with($data);

}

//HDMF CONTRIBUTION GET DATA LIST REPORT
public function getHDMFEmployeeContributionList(Request $request){

    $Reports = new Reports();

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    $param["Status"] = request("Status");
    $param["Filter"] = request("Filter");
    $param["SearchText"] = request("SearchText");

    $param["Year"] = request("Year");
    $param["Month"] = request("Month");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = ""; "";

    if($param["Status"]=='Approved'){
        $RetVal["HDMFEmployeeContributionList"] = $Reports->getHDMFApprovedEmployeeContribution($param);
        //Get Total Paging
        $param["PageNo"] = 0;
        $param["Limit"] = 0;
        $HDMFEmployeeContributionList = $Reports->getHDMFApprovedEmployeeContribution($param);
        $RetVal["TotalRecord"] = count($HDMFEmployeeContributionList);
    }else{
         $RetVal["HDMFEmployeeContributionList"] = $Reports->getHDMFPendingEmployeeContribution($param);
        //Get Total Paging
        $param["PageNo"] = 0;
        $param["Limit"] = 0;
        $HDMFEmployeeContributionList = $Reports->getHDMFPendingEmployeeContribution($param);
        $RetVal["TotalRecord"] = count($HDMFEmployeeContributionList);
    }

    return response()->json($RetVal);
}

// HDMF CONTRIBUTION PRINT REPORT
public function showAdminHDMFContributionPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }
   
  $Reports = new Reports();

  $data['Page'] = 'HDMF Contribution Print Report';
  $data = $this->SetAdminInitialData($data);

  $data['Year']=request("Year");  
  $data['Month']=request("Month");  

  $data["PageNo"] = request("PageNo");
  $data["Limit"] = request("Limit");
  $data["Status"] = request("Status");
  $data["Filter"] = request("Filter");
  $data["SearchText"] = request("SearchText");

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  if($data['Status']=='Approved'){
     $data['Limit']=0;  
    $data['PageNo']='0';  
    $data["HDMFEmployeeContributionList"] = $Reports->getHDMFApprovedEmployeeContribution($data);
  }else{
    $data['Limit']=0;  
    $data['PageNo']=0;  
    $data["HDMFEmployeeContributionList"] = $Reports->getHDMFPendingEmployeeContribution($data);
  }

  return View::make('admin/admin-hdmf-contribution-print-report')->with($data);
}

//PHIC CONTRIBUTION REPORT
public function showAdminPHICDeductionReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'PHIC Contribution Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

  return View::make('admin/admin-phic-contribution-report')->with($data);

}

//PHIC CONTRIBUTION REPORT DATA LIST
public function showAdminPHICContributionPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }
  
  $Reports = new Reports();

  $data['Page'] = 'PHIC Deduction Print Report';
  $data = $this->SetAdminInitialData($data);

  $data['Year']=request("Year");  
  $data['Month']=request("Month");  

  $data["PageNo"] = request("PageNo");
  $data["Limit"] = request("Limit");
  $data["Status"] = request("Status");
  $data["Filter"] = request("Filter");
  $data["SearchText"] = request("SearchText");

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

   if($data['Status']=='Approved'){
     $data['Limit']=0;  
    $data['PageNo']='0';  
    $data["PHICEmployeeContributionList"] = $Reports->getPHICApprovedEmployeeContribution($data);
  }else{
    $data['Limit']=0;  
    $data['PageNo']=0;  
    $data["PHICEmployeeContributionList"] = $Reports->getPHICPendingEmployeeContribution($data);
  }
  
  return View::make('admin/admin-phic-contribution-print-report')->with($data);

}

// PHIC CONTRIBUTION GET DATA LIST REPORT
public function getPHICEmployeeContributionList(Request $request){

    $Reports = new Reports();

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    $param["Status"] = request("Status");
    $param["Filter"] = request("Filter");
    $param["SearchText"] = request("SearchText");

    $param["Year"] = request("Year");
    $param["Month"] = request("Month");
    $param["Status"] = request("Status");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    if($param["Status"]=='Approved'){            
        $RetVal["PHICEmployeeContributionList"] = $Reports->getPHICApprovedEmployeeContribution($param);
        //Get Total Paging
        $param["PageNo"] = 0;
        $param["Limit"] = 0;
        $PHICEmployeeContributionList = $Reports->getPHICApprovedEmployeeContribution($param);
        $RetVal["TotalRecord"] = count($PHICEmployeeContributionList);
    }else{
         $RetVal["PHICEmployeeContributionList"] = $Reports->getPHICPendingEmployeeContribution($param);
        //Get Total Paging
        $param["PageNo"] = 0;
        $param["Limit"] = 0;
        $HDMFEmployeeContributionList = $Reports->getPHICPendingEmployeeContribution($param);
        $RetVal["TotalRecord"] = count($HDMFEmployeeContributionList);
    }



    return response()->json($RetVal);
}


// LOAN DEDUCTION REPORT
public function showAdminEmployeeLoanDeductionReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Loan Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  //Get Loan Type
  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;
  $LoanType = new LoanType();
  $data["LoanTypeList"] = $LoanType->getLoanTypeList($param);

  return View::make('admin/admin-employee-loan-report')->with($data);

}

// LOAN DEDUCTION REPORT
public function getPayrollTransactionEmployeeLoanDeductionListByFilter(Request $request){
    
    $Reports = new Reports();
    $ResponseMessage = "";

    $data["PayrollPeriodID"] = request("PayrollPeriodID");  
    $data["SearchText"] = request("SearchText");  
    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] = 0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

    if($data["Status"] =='Approved'){
        $RetVal["PayrollTransactionEmployeeLoanDeductionList"] = $Reports->getPayrollTransactionApprovedLoanDeduction($data);
    
        $data["PageNo"] = 0;
        $data["Limit"] = 0;
        $PayrollTransactionEmployeeLoanDeductionList = $Reports->getPayrollTransactionApprovedLoanDeduction($data);
        $RetVal['TotalRecord']=count($PayrollTransactionEmployeeLoanDeductionList);
    }else{
         $RetVal["PayrollTransactionEmployeeLoanDeductionList"] = $Reports->getPayrollTransactionPendingLoanDeduction($data);
    
        $data["PageNo"] = 0;
        $data["Limit"] = 0;
        $PayrollTransactionEmployeeLoanDeductionList = $Reports->getPayrollTransactionPendingLoanDeduction($data);
        $RetVal['TotalRecord']=count($PayrollTransactionEmployeeLoanDeductionList);
    }

    

   return response()->json($RetVal);

}

public function showAdminEmployeeLoanDeductionPrintReport(Request $request){


  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Loan Deduction Print Report';
  $data = $this->SetAdminInitialData($data);

  $data["PayrollPeriodID"] = request("PayrollPeriodID");  
  $data["SearchText"] = request("SearchText");  
  $data["PageNo"] = request("PageNo");
  $data["Limit"] = request("Limit");

  $RetVal['Response'] = "Success";
  $RetVal['ResponseMessage'] = "";

  $data["FilterType"] =  request("FilterType");
  $data["BranchID"] =  request("BranchID");
  $data["DivisionID"] =  request("DivisionID");
  $data["DepartmentID"] =  request("DepartmentID");
  $data["SectionID"] =  request("SectionID");
  $data["JobTypeID"] =  request("JobTypeID");
  $data["EmployeeID"] =  request("EmployeeID");
  $data["Status"] =  request("Status");

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
    }else{
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }

  $data['PrintingBatchNo']=request("PrintingBatchNo");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();
  $data['Reports']=$Reports; 

  $PayrollTransaction = new PayrollTransaction();
  $data['PayrollTransaction']=$PayrollTransaction; 

  $EmployeeDTR = new EmployeeDTR();
  $data['EmployeeDTR']=$EmployeeDTR; 

  $data['SearchText']='';  
  $data['Limit']=0;  
  $data['PageNo']=$data['PrintingBatchNo'];

  $EmployeeLoanDeductionList = $Reports->getPayrollTransactionLoanDeduction($data);
  if(count($EmployeeLoanDeductionList)>0){
    foreach ($EmployeeLoanDeductionList as $item) {
      $data['PayrollTransactionID']= $item->PayrollTransactionID;
    }
  }

 $data["EmployeeLoanDeductionList"] = $EmployeeLoanDeductionList;
 $data["PayrollTransactionInfo"]=$PayrollTransaction->getPayrollTransactionInfo($data['PayrollTransactionID']);

 return View::make('admin/admin-employee-loan-print-report')->with($data);

}

// EMPLOYEE LOAN LIST
public function getEmployeeLoanList(Request $request){

    $Reports = new Reports();

    $param["PageNo"] = request("PageNo");
    $param["Status"] = request("Status");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanList"] = $Reports->getEmployeeLoanTransaction($param);

    //Get Total Paging
    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $EmployeeLoanList = $Reports->getEmployeeLoanTransaction($param);
    $RetVal["TotalRecord"] = count($EmployeeLoanList);

    return response()->json($RetVal);
}

//EMPLOYEE OTHER DEDUCTIOn REPORT
public function showAdminEmployeeOtherDeductionReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Other Deduction Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  return View::make('admin/admin-employee-other-deduction-report')->with($data);

}

public function getPayrollTransactionEmployeeOtherDeductionListByFilter(Request $request){
    
    $Reports = new Reports();
    $ResponseMessage = "";

     $data["PayrollPeriodID"] = request("PayrollPeriodID");  
    $data["SearchText"] = request("SearchText");  
    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

    if( $data["Status"]=='Approved'){
        $RetVal["PayrollTransactionEmployeeOtherDeductionList"] = $Reports->getPayrollTransactionApprovedOtherDeduction($data);    
        $data["PageNo"] = 0;
        $data["Limit"] = 0;
        $PayrollTransactionEmployeeOtherDeductionList = $Reports->getPayrollTransactionApprovedOtherDeduction($data);
        $RetVal['TotalRecord']=count($PayrollTransactionEmployeeOtherDeductionList);
    }else{
        $RetVal["PayrollTransactionEmployeeOtherDeductionList"] = $Reports->getPayrollTransactionPendingOtherDeduction($data);    
        $data["PageNo"] = 0;
        $data["Limit"] = 0;
        $PayrollTransactionEmployeeOtherDeductionList = $Reports->getPayrollTransactionPendingOtherDeduction($data);
        $RetVal['TotalRecord']=count($PayrollTransactionEmployeeOtherDeductionList);
    }

  
   return response()->json($RetVal);

}

public function showAdminEmployeeOtherDeductionPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Other Deduction Print Report';
  $data = $this->SetAdminInitialData($data);

   $data["PayrollPeriodID"] = request("PayrollPeriodID");  
    $data["SearchText"] = request("SearchText");  
    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
    }else{
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }

  $data['PrintingBatchNo']=request("PrintingBatchNo");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();
  $data['Reports']=$Reports; 

  $PayrollTransaction = new PayrollTransaction();
  $data['PayrollTransaction']=$PayrollTransaction; 

  $EmployeeDTR = new EmployeeDTR();
  $data['EmployeeDTR']=$EmployeeDTR; 

  $data['SearchText']='';  
  $data['Limit']=0;  
  $data['PageNo']=$data['PrintingBatchNo'];

  $EmployeeOtherDeductionList = $Reports->getPayrollTransactionOtherDeduction($data);
  if(count($EmployeeOtherDeductionList)>0){
    foreach ($EmployeeOtherDeductionList as $item) {
      $data['PayrollTransactionID']= $item->PayrollTransactionID;
    }
  }

 $data["EmployeeOtherDeductionList"] = $EmployeeOtherDeductionList;
 $data["PayrollTransactionInfo"]=$PayrollTransaction->getPayrollTransactionInfo($data['PayrollTransactionID']);

  return View::make('admin/admin-employee-other-deduction-print-report')->with($data);

}

public function getEmployeeIncomeDeductionList(Request $request){

    $Reports = new Reports();
    
    $param["PageNo"] = request("PageNo");
    $param["Status"] = request("Status");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeIncomeDeductionList"] = $Reports->getEmployeeIncomeDeductionTransactionList($param);

        //Get Total Paging
    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $EmployeeIncomeDeductionList = $Reports->getEmployeeIncomeDeductionTransactionList($param);
    $RetVal["TotalRecord"] = count($EmployeeIncomeDeductionList);

    return response()->json($RetVal);
}

//EMPLOYEE EARNING TAXABLE REPORT
public function showAdminEmployeeIncomeTaxableReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Income Taxable Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  //Get Income List
  $param["SearchText"] = ""; 
  $param["Status"] = "Taxable Income";
  $param["PageNo"] = 0;
  $param["Limit"] =  0;
  $IncomeDeductionType = new IncomeDeductionType();
  $data["IncomeDeductionTypeList"] = $IncomeDeductionType->getIncomeDeductionTypeList($param);

  return View::make('admin/admin-employee-income-taxable-report')->with($data);

}

public function getPayrollTransactionEmployeeIncomeTaxableListByFilter(Request $request){
    
  $Reports = new Reports();
  $ResponseMessage = "";

    $data["PayrollPeriodID"] = request("PayrollPeriodID");  
    $data["SearchText"] = request("SearchText");  
    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

    if($data["Status"]=='Approved'){
      $RetVal["PayrollTransactionEmployeeIncomeTaxableList"] = $Reports->getPayrollTransactionApprovedOtherEarningTaxable($data);
    
      $data["PageNo"] = 0;
      $data["Limit"] = 0;
      $PayrollTransactionEmployeeIncomeTaxableList = $Reports->getPayrollTransactionApprovedOtherEarningTaxable($data);
      $RetVal['TotalRecord']=count($PayrollTransactionEmployeeIncomeTaxableList);
    }else{
       $RetVal["PayrollTransactionEmployeeIncomeTaxableList"] = $Reports->getPayrollTransactionPendingOtherEarningTaxable($data);
    
      $data["PageNo"] = 0;
      $data["Limit"] = 0;
      $PayrollTransactionEmployeeIncomeTaxableList = $Reports->getPayrollTransactionPendingOtherEarningTaxable($data);
      $RetVal['TotalRecord']=count($PayrollTransactionEmployeeIncomeTaxableList);
    }

    
   return response()->json($RetVal);

}

public function showAdminEmployeeIncomeTaxablePrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Income Taxable Print Report';
  $data = $this->SetAdminInitialData($data);

  $data["PayrollPeriodID"] = request("PayrollPeriodID");  
  $data["SearchText"] = request("SearchText");  
  $data["PageNo"] = request("PageNo");

  $data["FilterType"] =  request("FilterType");
  $data["BranchID"] =  request("BranchID");
  $data["SiteID"] =  request("SiteID");
  $data["DivisionID"] =  request("DivisionID");
  $data["DepartmentID"] =  request("DepartmentID");
  $data["SectionID"] =  request("SectionID");
  $data["JobTypeID"] =  request("JobTypeID");
  $data["EmployeeID"] =  request("EmployeeID");
  $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
    }else{
      $data["BranchID"] =  0;
      $data["SiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }

  $data['PrintingBatchNo']=request("PrintingBatchNo");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();
  $data['Reports']=$Reports; 

  $PayrollTransaction = new PayrollTransaction();
  $data['PayrollTransaction']=$PayrollTransaction; 

  $EmployeeDTR = new EmployeeDTR();
  $data['EmployeeDTR']=$EmployeeDTR; 

  $data['SearchText']='';  
  $data['Limit']=0;  
  $data['PageNo']=$data['PrintingBatchNo'];
  
  $EmployeeOtherEarningTaxableList = $Reports->getPayrollTransactionOtherEarningTaxable($data);
  if(count($EmployeeOtherEarningTaxableList)>0){
    foreach ($EmployeeOtherEarningTaxableList as $item) {
      $data['PayrollTransactionID']= $item->PayrollTransactionID;
    }
  }

 $data["EmployeeOtherEarningTaxableList"] = $EmployeeOtherEarningTaxableList;
 $data["PayrollTransactionInfo"]=$PayrollTransaction->getPayrollTransactionInfo($data['PayrollTransactionID']);

 return View::make('admin/admin-employee-income-taxable-print-report')->with($data);

}

//EMPLOYEE EARNING NON TAXABLE REPORT
public function showAdminEmployeeIncomeNonTaxableReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Income Non Taxable Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  //Get Income List
  $param["SearchText"] = ""; 
  $param["Status"] = "Non-Taxable Income";
  $param["PageNo"] = 0;
  $param["Limit"] =  0;
  $IncomeDeductionType = new IncomeDeductionType();
  $data["IncomeDeductionTypeList"] = $IncomeDeductionType->getIncomeDeductionTypeList($param);

  return View::make('admin/admin-employee-income-non-taxable-report')->with($data);

}

public function getPayrollTransactionEmployeeIncomeNonTaxableListByFilter(Request $request){
    
    $Reports = new Reports();
    $ResponseMessage = "";

     $data["PayrollPeriodID"] = request("PayrollPeriodID");  
    $data["SearchText"] = request("SearchText");  
    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

   if($data["Status"]=='Approved'){
        $RetVal["PayrollTransactionEmployeeIncomeNonTaxableList"] = $Reports->getPayrollTransactionOtherApprovedEarningNonTaxable($data);

        //Get Total Count of Payroll Transaction Loan Deduction
        $data["PageNo"] = 0;
        $data["Limit"] = 0;
        $PayrollTransactionEmployeeIncomeNonTaxableList = $Reports->getPayrollTransactionOtherApprovedEarningNonTaxable($data);
        $RetVal['TotalRecord']=count($PayrollTransactionEmployeeIncomeNonTaxableList);
   }else{
       $RetVal["PayrollTransactionEmployeeIncomeNonTaxableList"] = $Reports->getPayrollTransactionOtherPendingEarningNonTaxable($data);

        //Get Total Count of Payroll Transaction Loan Deduction
        $data["PageNo"] = 0;
        $data["Limit"] = 0;
        $PayrollTransactionEmployeeIncomeNonTaxableList = $Reports->getPayrollTransactionOtherPendingEarningNonTaxable($data);
        $RetVal['TotalRecord']=count($PayrollTransactionEmployeeIncomeNonTaxableList);
   }
 
   return response()->json($RetVal);

}

public function showAdminEmployeeIncomeNonTaxablePrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Other Earning Non Taxable Print Report';
  $data = $this->SetAdminInitialData($data);

  $data["PayrollPeriodID"] = request("PayrollPeriodID");  
  $data["SearchText"] = request("SearchText");  
  $data["PageNo"] = request("PageNo");

  $data["FilterType"] =  request("FilterType");
  $data["BranchID"] =  request("BranchID");
  $data["DivisionID"] =  request("DivisionID");
  $data["DepartmentID"] =  request("DepartmentID");
  $data["SectionID"] =  request("SectionID");
  $data["JobTypeID"] =  request("JobTypeID");
  $data["EmployeeID"] =  request("EmployeeID");
  $data["Status"] =  request("Status");

  if($data["FilterType"] == "Location"){
    $data["SiteID"] =  [];
    $data["DivisionID"] =  [];
    $data["DepartmentID"] = [];
    $data["SectionID"] = [];
    $data["JobTypeID"] = [];
    $data["EmployeeID"] =  0;
  }else if($data["FilterType"] == "Site"){
    $data["BranchID"] =  [];
    $data["DivisionID"] =  [];
    $data["DepartmentID"] = [];
    $data["SectionID"] = [];
    $data["JobTypeID"] = [];
    $data["EmployeeID"] =  0;
  }else if($data["FilterType"] == "Division"){
    $data["BranchID"] =  [];
    $data["SiteID"] =  [];
    $data["DepartmentID"] = [];
    $data["SectionID"] = [];
    $data["JobTypeID"] = [];
    $data["EmployeeID"] =  0;
  }else if($data["FilterType"] == "Department"){
    $data["BranchID"] =  [];
    $data["SiteID"] =  [];
    $data["DivisionID"] =  [];
    $data["SectionID"] = [];
    $data["JobTypeID"] = [];
    $data["EmployeeID"] =  0;
  }else if($data["FilterType"] == "Section"){
    $data["BranchID"] =  [];
    $data["SiteID"] =  [];
    $data["DivisionID"] =  [];
    $data["DepartmentID"] = [];
    $data["JobTypeID"] = [];
    $data["EmployeeID"] =  0;
  }else if($data["FilterType"] == "Job Type"){
    $data["BranchID"] =  [];
    $data["SiteID"] =  [];
    $data["DivisionID"] =  [];
    $data["DepartmentID"] = [];
    $data["SectionID"] = [];
    $data["EmployeeID"] =  0;
  }else if($data["FilterType"] == "Employee"){
    $data["BranchID"] =  [];
    $data["SiteID"] =  [];
    $data["DivisionID"] =  [];
    $data["DepartmentID"] = [];
    $data["SectionID"] = [];
  }else{
    $data["BranchID"] =  [];
    $data["SiteID"] =  [];
    $data["DivisionID"] =  [];
    $data["DepartmentID"] = [];
    $data["SectionID"] = [];
    $data["JobTypeID"] = [];
    $data["EmployeeID"] =  0;
  }

  $data['PrintingBatchNo']=request("PrintingBatchNo");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();
  $data['Reports']=$Reports; 

  $PayrollTransaction = new PayrollTransaction();
  $data['PayrollTransaction']=$PayrollTransaction; 

  $EmployeeDTR = new EmployeeDTR();
  $data['EmployeeDTR']=$EmployeeDTR; 

  $data['SearchText']='';  
  $data['Limit']=0;  
  $data['PageNo']=$data['PrintingBatchNo'];
  
  $EmployeeOtherEarningNonTaxableList = $Reports->getPayrollTransactionOtherEarningNonTaxable($data);
  if(count($EmployeeOtherEarningNonTaxableList)>0){
    foreach ($EmployeeOtherEarningNonTaxableList as $item) {
      $data['PayrollTransactionID']= $item->PayrollTransactionID;
    }
  }

 $data["EmployeeOtherEarningNonTaxableList"] = $EmployeeOtherEarningNonTaxableList;
 $data["PayrollTransactionInfo"]=$PayrollTransaction->getPayrollTransactionInfo($data['PayrollTransactionID']);

  return View::make('admin/admin-employee-income-non-taxable-print-report')->with($data);

}


//PAYSLIP REPORT
public function showAdminEmployeePayslipReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Payslip Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  return View::make('admin/admin-employee-payslip-report')->with($data);

}

public function getPayrollTransactionEmployeePayslipList(Request $request){ 

    $Reports = new Reports();

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");
    $data["PayrollPeriodID"] =  request("PayrollPeriodID");

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["BranchSiteID"] =  request("BranchSiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");
    $data["Status"] =  request("Status");

    $data["SearchText"] = '';

    if($data["FilterType"] == "Location"){
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
    }else{
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }
    
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeePaySlipReport"] =  $Reports->getEmployeePaySlipReport($data);

    $data["PageNo"] = 0;
    $data["Limit"] = 0;
    $EmployeePaySlipReport =  $Reports->getEmployeePaySlipReportCount($data);  
    $RetVal["TotalRecord"] = count($EmployeePaySlipReport);

    return response()->json($RetVal);

}

public function showAdminEmployeePayslipPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee Payslip Print Report';
  $data = $this->SetAdminInitialData($data);

  $data["PayrollPeriodID"] = request("PayrollPeriodID");  
  $data["SearchText"] = request("SearchText");  
  $data["PageNo"] = request("PageNo");


  $data["FilterType"] =  request("FilterType");
  $data["BranchID"] =  request("BranchID");
  $data["BranchSiteID"] =  request("BranchSiteID");
  $data["DivisionID"] =  request("DivisionID");
  $data["DepartmentID"] =  request("DepartmentID");
  $data["SectionID"] =  request("SectionID");
  $data["JobTypeID"] =  request("JobTypeID");
  $data["EmployeeID"] =  request("EmployeeID");
  $data["Status"] =  request("Status");

    if($data["FilterType"] == "Location"){
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
    }else{
      $data["BranchID"] =  0;
      $data["BranchSiteID"] =  0;
      $data["DivisionID"] =  0;
      $data["DepartmentID"] = 0;
      $data["SectionID"] = 0;
      $data["JobTypeID"] = 0;
      $data["EmployeeID"] =  0;
    }

  $data['PrintingBatchNo']=request("PrintingBatchNo");  

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();
  $data['Reports']=$Reports; 

  $PayrollTransaction = new PayrollTransaction();
  $data['PayrollTransaction']=$PayrollTransaction; 

  $EmployeeDTR = new EmployeeDTR();
  $data['EmployeeDTR']=$EmployeeDTR; 

  $data['SearchText']='';  
  $data['Limit']=100;  
  $data['PageNo']=$data['PrintingBatchNo'];

  $PayrollTransactionEmployeeList = $Reports->getPayrollTransactionEmployeeListByFilter($data);

  if(count($PayrollTransactionEmployeeList)>0){
    foreach ($PayrollTransactionEmployeeList as $item) {
      $data['PayrollTransactionID']= $item->PayrollTransactionID;
    }
  }
  
  $data["PayrollTransactionEmployeeList"]=$PayrollTransactionEmployeeList;
  $data["PayrollTransactionInfo"]=$PayrollTransaction->getPayrollTransactionInfo($data['PayrollTransactionID']);

  return View::make('admin/admin-employee-payslip-print-report')->with($data);

}

// PAYROLL JOURNAL REPORT
public function showAdminPayrollJournalReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Journal Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSiteList"] = $BranchSite->getBranchSiteList($param);

  $Division = new Division();
  $data["DivisionList"] = $Division->getDivisionList($param);

  $Department = new Department();
  $data["DepartmentList"] = $Department->getDepartmentList($param);

  $Section = new Section();
  $data["SectionList"] = $Section->getSectionList($param);

  $JobType = new JobType();
  $data["JobTypeList"] = $JobType->getJobTypeList($param);
  
  return View::make('admin/admin-payroll-journal-report')->with($data);

}

public function getPayrollJournalReportList(Request $request){ 

    $Reports = new Reports();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");
    $data["PeriodYear"] =  request("PeriodYear");
    $data["FilterType"] =  request("FilterType");
    $data["Status"] =  request("Status");

    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");

    if($data["FilterType"] == "Location"){
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
    }

    $data["SearchText"] = '';

    $RetVal["PayrollJournalReport"] =  $Reports->getPayrollJournalReport($data);

    if($data["Limit"] != 0){
      $data["PageNo"] = 0;
      $data["Limit"] = 0;
      $PayrollApprovedJournalReport =  $Reports->getPayrollJournalReportCount($data);  
      $RetVal["TotalRecord"] = count($PayrollApprovedJournalReport);
    }
    
    return response()->json($RetVal);

}


public function showAdminPayrollJournalPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Journal Print Report';
  $data = $this->SetAdminInitialData($data);

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();  
  $data["Year"] =  request("YearCover");

  $data['SearchText']='';  
  $data['Limit']='';  
  $data['PageNo']='';

  $PayrollJournalList= $Reports->getPayrollJournalReport($data);
  $data["PayrollJournalList"]=$PayrollJournalList;

  return View::make('admin/admin-payroll-journal-print-report')->with($data);

}

// PAYROLL REGISTER REPORT
public function showAdminPayrollRegisterReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Register Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  return View::make('admin/admin-payroll-register-report')->with($data);

}

public function showAdminPayrollRegisterPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Register Print Report';
  $data = $this->SetAdminInitialData($data);

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $Reports = new Reports();
  $PayrollTransaction = new PayrollTransaction();

  $data['Reports']=$Reports; 
  $data["PayrollPeriodID"] =  request("PayrollPeriodID");

  $param['SearchText']='';  
  $param['Limit']=0;  
  $param['PageNo']=0;
  $param['Status']='';

  $PayrollTransactionEmployeeList= $Reports->getPayrollRegisterReport($param);

  if(count($PayrollTransactionEmployeeList)>0){
    foreach ($PayrollTransactionEmployeeList as $item) {
      $data['PayrollTransactionID']= $item->PayrollTransactionID;
    }
  }
 
  $data["PayrollTransactionEmployeeList"]=$PayrollTransactionEmployeeList;
  $data["PayrollTransactionInfo"]=$PayrollTransaction->getPayrollTransactionInfo($data['PayrollTransactionID']); 

  return View::make('admin/admin-payroll-register-print-report')->with($data);

}

public function getPayrollRegisterReportList(Request $request){

    $Reports = new Reports();

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $data["PayrollPeriodID"] =  request("PayrollPeriodID");
    $data["Status"] =  request("Status");

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");


    $data["SearchText"] = '';

    if($data["FilterType"] == "Location"){
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

   if($data["Status"]=='Approved'){

       $RetVal["PayrollRegisterReport"] = $Reports->getPayrollRegisterApprovedReport($data);

       $data["PageNo"] = 0;
       $data["Limit"] = 0;
       $PayrollRegisterReportCount = $Reports->getPayrollRegisterApprovedReportCount($data);
       $RetVal["TotalRecord"] = count($PayrollRegisterReportCount);

   }else{

      $RetVal["PayrollRegisterReport"] = $Reports->getPayrollRegisterPendingReport($data);
      
      $data["PageNo"] = 0;
      $data["Limit"] = 0;
      $PayrollRegisterReportCount = $Reports->getPayrollRegisterPendingReport($data);
      $RetVal["TotalRecord"] = count($PayrollRegisterReportCount);

   }
    
   return response()->json($RetVal);
}

// PAYROLL RAW DATA REPORT
public function showAdminPayrollRawDataReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Raw Data Report';
  $data = $this->SetAdminInitialData($data);

  $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  $OTRate = new OTRate();
  $data["OTRateModel"] = $OTRate;
  $data["OTRateList"] = $OTRate->getOTRateList($param);

  $LeaveType = new LeaveType();
  $data["LeaveTypeModel"] = $LeaveType;
  $data["LeaveTypeList"] = $LeaveType->getLeaveTypeList($param);

  return View::make('admin/admin-payroll-raw-data-report')->with($data);

}

public function getPayrollRawDataReportList(Request $request){

    $Reports = new Reports();

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $data["PayrollPeriodID"] =  request("PayrollPeriodID");
    $data["Status"] =  request("Status");

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");


    $data["SearchText"] = '';

    if($data["FilterType"] == "Location"){
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] = 0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] = 0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

   if($data["Status"]=='Approved'){

      $RetVal["PayrollRegisterReport"] = $Reports->getPayrollApprovedRawDataReport($data);

       $data["PageNo"] = 0;
       $data["Limit"] = 0;
       $PayrollRegisterReportCount = $Reports->getPayrollApprovedRawDataReportCount($data);
       $RetVal["TotalRecord"] = count($PayrollRegisterReportCount);

   }else{

      $RetVal["PayrollRegisterReport"] = $Reports->getPayrollPendingRawDataReport($data);
      
      $data["PageNo"] = 0;
      $data["Limit"] = 0;
      $PayrollRegisterReportCount = $Reports->getPayrollPendingRawDataReportCount($data);
      $RetVal["TotalRecord"] = count($PayrollRegisterReportCount);

   }
    
   return response()->json($RetVal);
}

// EMPLOYEE DTR REPORT
public function showAdminEmployeeDTRSummaryReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee DTR Report';
  $data = $this->SetAdminInitialData($data);
 $param["SearchText"] = "";  
  $param["Status"] = "";
  $param["PageNo"] = 0;
  $param["Limit"] = 0;

  $PayrollPeriod = new PayrollPeriod();
  $data["PayrollPeriodList"] = $PayrollPeriod->getPayrollPeriodList($param);

  $Branch = new Branch();
  $data["BranchList"] = $Branch->getBranchList($param);

  $BranchSite = new BranchSite();
  $data["BranchSite"] = $BranchSite->getBranchSiteList($param);

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

  return View::make('admin/admin-employee-dtr-report')->with($data);

}

public function showAdminEmployeeDTRSummaryPrintReport(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Employee DTR Summary Print Report';
  $data = $this->SetAdminInitialData($data);

  $PayrollSetting = new PayrollSetting();
  $data['CompanyInfo']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  $data["PayrollPeriodID"] =  request("PayrollPeriodID");
  $data['Limit']='';  
  $data['PageNo']='';
  
  $Reports = new Reports();
  $data["DTRPrintReportList"] = $Reports->getEmployeeDTRReport($data);

  return View::make('admin/admin-employee-dtr-print-report')->with($data);

}

public function getEmployeeDTRReportList(Request $request){

    $Reports = new Reports();

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");

    $data["PayrollPeriodID"] =  request("PayrollPeriodID");
    $data["Status"] =  request("Status");

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");


    $data["SearchText"] = '';

    if($data["FilterType"] == "Location"){
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Site"){
      $data["BranchID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Division"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Section"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Job Type"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["EmployeeID"] =  0;
    }else if($data["FilterType"] == "Employee"){
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
    }else{
      $data["BranchID"] =  [];
      $data["SiteID"] =  [];
      $data["DivisionID"] =  [];
      $data["DepartmentID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] =  0;
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeDTRReport"] =  $Reports->getEmployeeDTRReport($data);

    //Get Total Paging
    $data["PageNo"] = 0;
    $data["Limit"] = 0;
    $EmployeeDTRReport = $Reports->getEmployeeDTRReportCount($data);
    $RetVal["TotalRecord"] = count($EmployeeDTRReport);

    return response()->json($RetVal);
}

}



