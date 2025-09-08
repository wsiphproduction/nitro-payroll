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
use App\Models\GenerateExcel;
use App\Models\Reports;

class GenerateExcelController extends Controller {
 
  // EMPLOYEE EXCEL LIST
  public function getExcelEmployeeList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllEmployeeExcelList=$GenerateExcel->generateEmployeeListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllEmployeeExcelList"] = $AllEmployeeExcelList;

    return response()->json($RetVal);

  }
  
  // LOAN TYPE EXCEL LIST
  public function getExcelLoanTypeList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllLoanTypeList=$GenerateExcel->generateLoanTypeListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllLoanTypeList"] = $AllLoanTypeList;

    return response()->json($RetVal);

  }

    // OT RATE LIST
  public function getExcelOTRatesList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllOTRateList=$GenerateExcel->generateOTRatesListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllOTRateList"] = $AllOTRateList;

    return response()->json($RetVal);

  }
  
  // INCOME DEDUCTION TYPE EXCEL LIST
  public function getExcelIncomeDeductionTypeList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllIncomeDeductionTypeList=$GenerateExcel->generateIncomeDeductionTypeListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllIncomeDeductionTypeList"] = $AllIncomeDeductionTypeList;

    return response()->json($RetVal);

  }
 
 // PAYROLL PERIOD EXCEL LIST
  public function getExcelPayrollPeriodList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllPayrollPeriodList=$GenerateExcel->generatePayrollPeriodListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllPayrollPeriodList"] = $AllPayrollPeriodList;

    return response()->json($RetVal);

  }

//SSS BRACKET EXCEL LIST
public function getExcelSSSBracketList(Request $request){

   $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllSSSBracketList=$GenerateExcel->generateSSSTableBracketListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllSSSBracketList"] = $AllSSSBracketList;

    return response()->json($RetVal);

}

//HDMF BRACKET EXCEL LIST
public function getExcelHDMFBracketList(Request $request){

   $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllHDMFBracketList=$GenerateExcel->generateHDMFTableBracketListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllHDMFBracketList"] = $AllHDMFBracketList;

    return response()->json($RetVal);

}

//PHIC BRACKET EXCEL LIST
public function getExcelPHICBracketList(Request $request){

   $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllPHICBracketList=$GenerateExcel->generatePHICTableBracketListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllPHICBracketList"] = $AllPHICBracketList;

    return response()->json($RetVal);

}

//ANNUAL INCOME TAX BRACKET EXCEL LIST
public function getExcelAnnualIncomeTaxList(Request $request){

   $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllAnnualIncomeTaxList=$GenerateExcel->generateAnnualIncomeTaxListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllAnnualIncomeTaxList"] = $AllAnnualIncomeTaxList;

    return response()->json($RetVal);

}

//WITHHOLDING INCOME BRACKET EXCEL LIST
public function getExcelWithHoldingTaxList(Request $request){

   $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
    $AllWithHoldingTaxList=$GenerateExcel->generateWithHoldingTaxListExcel();

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllWithHoldingTaxList"] = $AllWithHoldingTaxList;

    return response()->json($RetVal);

}

// PAYROLL JOURNAL EXCEL
  public function getExcelPayrollJournalList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
        
    $data["PayrollPeriodID"] =  request("PayrollPeriodID");

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

    if( $data["Status"]=='Approved'){
      $PayrollJournalExcelList=$GenerateExcel->generatePayrollApprovedJournalListExcel($data);

      $RetVal['Response'] = "Success";
      $RetVal['ResponseMessage'] = "";
      $RetVal["PayrollJournalExcelList"] = $PayrollJournalExcelList;
    }else{
        $PayrollJournalExcelList=$GenerateExcel->generatePayrollPendingJournalListExcel($data);

      $RetVal['Response'] = "Success";
      $RetVal['ResponseMessage'] = "";
      $RetVal["PayrollJournalExcelList"] = $PayrollJournalExcelList;
    }

    

    return response()->json($RetVal);

}

// PAYROLL REGISTER EXCEL
  public function getExcelPayrollRegisterList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
        
    $data["PayrollPeriodID"] =  request("PayrollPeriodID");

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
      $data["EmployeeID"] = 0;
    }else if($data["FilterType"] == "Department"){
      $data["BranchID"] = [];
      $data["SiteID"] = [];
      $data["DivisionID"] = [];
      $data["SectionID"] = [];
      $data["JobTypeID"] = [];
      $data["EmployeeID"] = 0;
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
      $PayrollRegisterExcelList=$GenerateExcel->generatePayrollRegisterApprovedListExcel($data);
    }else{
      $PayrollRegisterExcelList=$GenerateExcel->generatePayrollRegisterPendingListExcel($data);
    }
    
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollRegisterExcelList"] = $PayrollRegisterExcelList;

    return response()->json($RetVal);

}

// PAYROLL RAW DATA LIST EXCEL
  public function getExcelPayrollRawDataList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    
        
    $data["PayrollPeriodID"] =  request("PayrollPeriodID");

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
      $PayrollRawDataExcelList=$GenerateExcel->getPayrollApprovedRawDataListExcel($data);
    }else{
      $PayrollRawDataExcelList=$GenerateExcel->getPayrollPendingRawDataListExcel($data);
    }
    
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PayrollRawDataExcelList"] = $PayrollRawDataExcelList;

    return response()->json($RetVal);

}

// EMPLOYEE DTR EXCEL
  public function getExcelEmployeeDTRList(Request $request){

    $GenerateExcel = new GenerateExcel();

  
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

    $EmployeeDTRExcelList=$GenerateExcel->generateEmployeeDTRListExcel($data);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeDTRExcelList"] = $EmployeeDTRExcelList;

    return response()->json($RetVal);

}

// SSS CONTRIBUTION EXCEL
  public function getExcelSSSContributionList(Request $request){

    $GenerateExcel = new GenerateExcel();
    $Reports= new Reports();

    $Platform=request("Platform");    

    $data['Year']=request("Year");  
    $data['Month']=request("Month");  

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");
    $data["Status"] = request("Status");
    $data["Filter"] = request("Filter");
    $data["SearchText"] = request("SearchText");

    if($data["Status"]=='Approved'){
      $data['Limit']=0;  
      $data['PageNo']='0';  
     $SSSContributionExcelList=$GenerateExcel->generateSSSApprovedEmployeeContributionListExcel($data);
    }else{
      $data['Limit']=0;  
      $data['PageNo']='0';  
      $SSSContributionExcelList=$GenerateExcel->generateSSSPendingEmployeeContributionListExcel($data);
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["SSSContributionExcelList"] = $SSSContributionExcelList;

    return response()->json($RetVal);

}

// HDMF CONTRIBUTION EXCEL
  public function getExcelHDMFContributionList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    

    $data['Year']=request("Year");  
    $data['Month']=request("Month");  

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");
    $data["Status"] = request("Status");
    $data["Filter"] = request("Filter");
    $data["SearchText"] = request("SearchText");

    if($data["Status"]=='Approved'){
      $data['Limit']=0;  
      $data['PageNo']='0';  
     $HDMFContributionExcelList=$GenerateExcel->generateHDMFApprovedEmployeeContributionListExcel($data);
    }else{
      $data['Limit']=0;  
      $data['PageNo']='0';  
      $HDMFContributionExcelList=$GenerateExcel->generateHDMFPendingEmployeeContributionListExcel($data);
    }
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["HDMFContributionExcelList"] = $HDMFContributionExcelList;

    return response()->json($RetVal);

}

// PHIC CONTRIBUTION EXCEL
  public function getExcelPHICContributionList(Request $request){

    $GenerateExcel = new GenerateExcel();

     $Platform=request("Platform");    

    $data['Year']=request("Year");  
    $data['Month']=request("Month");  

    $data["PageNo"] = request("PageNo");
    $data["Limit"] = request("Limit");
    $data["Status"] = request("Status");
    $data["Filter"] = request("Filter");
    $data["SearchText"] = request("SearchText");

   if($data["Status"]=='Approved'){
      $data['Limit']=0;  
      $data['PageNo']='0';  
      $PHICContributionExcelList=$GenerateExcel->generatePHICApprovedEmployeeContributionListExcel($data);
    }else{
       $data['Limit']=0;  
      $data['PageNo']='0';  
      $PHICContributionExcelList=$GenerateExcel->generatePHICPendingEmployeeContributionListExcel($data);
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["PHICContributionExcelList"] = $PHICContributionExcelList;

    return response()->json($RetVal);

}

// EMPLOYEE LOAN DEDUCTION EXCEL
  public function getExcelEmployeeLoanDeductionList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    

    $data["PayrollPeriodID"]=request("PayrollPeriodID"); 
    $data["Status"] =  request("Status");  

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");    

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

    if($data["Status"] =='Approved'){
      $EmployeeLoanDeductionExcelList=$GenerateExcel->generateEmployeeApprovedLoanDeductionListExcel($data);      
     }else{
      $EmployeeLoanDeductionExcelList=$GenerateExcel->generateEmployeePendingLoanDeductionListExcel($data);
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeLoanDeductionExcelList"] = $EmployeeLoanDeductionExcelList;
  
    return response()->json($RetVal);
}

// EMPLOYEE OTHER DEDUCTION EXCEL
  public function getExcelEmployeeOtherDeductionList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    

    $data["PayrollPeriodID"]=request("PayrollPeriodID"); 
    $data["Status"] =  request("Status");  

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");    

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
      $data["SectionID"] = 0;
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
      $EmployeeOtherDeductionExcelList=$GenerateExcel->generateEmployeeApprovedOtherDeductionListExcel($data);        
     }else{
     $EmployeeOtherDeductionExcelList=$GenerateExcel->generateEmployeePendingOtherDeductionListExcel($data);
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeOtherDeductionExcelList"] = $EmployeeOtherDeductionExcelList;

    return response()->json($RetVal);

}

// EMPLOYEE OTHER EARRNING TAXABLE EXCEL
  public function getExcelEmployeeOtherEarningTaxableList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    

    $data["PayrollPeriodID"]=request("PayrollPeriodID"); 
    $data["Status"] =  request("Status");  

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");    

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
      $data["EmployeeID"] = 0;
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
      $EmployeeOtherEarningTaxableExcelList=$GenerateExcel->generateEmployeeApprovedOtherEarningTaxableListExcel($data);        
     }else{
     $EmployeeOtherEarningTaxableExcelList=$GenerateExcel->generateEmployeePendingOtherEarningTaxableListExcel($data);
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeOtherEarningTaxableExcelList"] = $EmployeeOtherEarningTaxableExcelList;

    return response()->json($RetVal);

}

// EMPLOYEE OTHER EARRNING NON TAXABLE EXCEL
  public function getExcelEmployeeOtherEarningNonTaxableList(Request $request){

    $GenerateExcel = new GenerateExcel();

    $Platform=request("Platform");    

    $data["PayrollPeriodID"]=request("PayrollPeriodID"); 
    $data["Status"] =  request("Status");  

    $data["FilterType"] =  request("FilterType");
    $data["BranchID"] =  request("BranchID");
    $data["SiteID"] =  request("SiteID");
    $data["DivisionID"] =  request("DivisionID");
    $data["DepartmentID"] =  request("DepartmentID");
    $data["SectionID"] =  request("SectionID");
    $data["JobTypeID"] =  request("JobTypeID");
    $data["EmployeeID"] =  request("EmployeeID");    

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

    if($data["Status"] =='Approved'){
      $EmployeeOtherEarningNonTaxableExcelList=$GenerateExcel->generateEmployeeOtherApprovedEarningNonTaxableListExcel($data);        
     }else{
     $EmployeeOtherEarningNonTaxableExcelList=$GenerateExcel->generateEmployeeOtherPendingEarningNonTaxableListExcel($data);
    }

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["EmployeeOtherEarningNonTaxableExcelList"] = $EmployeeOtherEarningNonTaxableExcelList;

    return response()->json($RetVal);

  }

}



